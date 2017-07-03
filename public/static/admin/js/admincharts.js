function ChartX(setting){
    if(!Array.isArray(setting)){
        if(!typeof setting == "object" || !typeof setting == "undefined" ){
            throw new TypeError("提供给ChartX构造函数的参数（setting）类型需要是object类型，当前类型是"+typeof setting)
        }
    }else{
        throw new TypeError("提供给ChartX构造函数的参数（setting）类型需要是object类型，当前类型是Array")
    };
    this.default = {
        width: 100,         //占父容器的宽度
        height: 100,        //占父容器的高度
        padding: 40,        //画布的填充，一些表粒度文字和标识性内容所在位置
        bgc: "#f5f5f5",     //画布的背景色
        type: 1,            //1为趋势图（柱图、折线图），0为占比图（饼图、圈）
        color: ["#0bb4c8","#ef2424","#b109db","#ecca0d","#f86706","#5b5b5b","#63bf0a","#2f3ed1","#989898"],//不同内容的数据标识颜色
        xU: "",             //x轴坐标的单位，默认是空字符
        yU: ""              //y轴坐标的单位，默认是空字符
    };
    var opt = (function(){
        var i,
            len = arguments.length;
        for(var i = 1;i<len;i++){
            for(var name in arguments[i]){
                arguments[0][name] = arguments[i][name]
            }
        };
        return arguments[0]
    })({},this.default,setting);
    
    /**
    趋势图坐标轴初始化
    */
    var initChartCol = function(eleId){
        if(typeof eleId != "string"){
            throw new TypeError("参数eleId("+eleId+")必须是个节点ID名，需要是String类型，或者是个返回String类型的表达式，当前的类型是"+typeof eleId)
        }else if(document.getElementById(eleId)){
            var canvas = document.createElement("canvas");
            var wrap = document.getElementById(eleId);
            if(canvas.getContext){
                wrap.appendChild(canvas);
                var context = canvas.getContext("2d");
                var height = Math.floor(opt.height*wrap.clientHeight/100);
                var width = Math.floor(opt.width*wrap.clientWidth/100);
                canvas.height = height;
                canvas.width = width;
                canvas.style.backgroundColor = opt.bgc;
                context.lineWidth = 1;
                context.strokeStyle = "#333";
                context.beginPath()
                context.translate(opt.padding,opt.padding);
                
                context.moveTo(0,0);
                context.lineTo(0,height-2*opt.padding);
                context.lineTo(width-2*opt.padding,height-2*opt.padding);
                context.stroke();
                return {
                        ct: context,                            //画布2d上线文对象
                        height: height-2*opt.padding,           //已经减去上下两侧的填充了
                        width: width-2*opt.padding              //已经减去左右两侧的填充了
                }
            }else{
                wrap.innerHTML = "您的浏览器不支持画布功能";
            }
        }else{
            throw new ReferenceError("id为"+eleId+"的节点并不存在，你的图表插异次元空间了！")
        }
    };
    this.draw = function(eleId,data){
        if(opt.type == 1){
            console.log("绘制趋势图");
            //整理数据
            var dataVal = (function(d){
                var dv = {
                        len : 0,
                        names : [],
                        vals : [],
                        xR: d.xR,
                        max: Number.NEGATIVE_INFINITY,
                        min: Number.POSITIVE_INFINITY
                    };
                for(var name in d.value){
                    dv.names.push(name);
                    for(var num in d.value[name]){
                        if(d.value[name][num] < 0) d.value[name][num] = 0;
                        if(d.value[name][num] > dv.max) dv.max = d.value[name][num]
                        if(d.value[name][num] < dv.min) dv.min = d.value[name][num]
                    }
                    dv.vals.push(d.value[name])
                    dv.len++;
                }
                return dv;
            })(data);
            var yR = (function(num){
                var max = Math.floor(num),
                    maxString = String(max),
                    maxLen = maxString.length,
                    maxFirst = Number(maxString[0]),
                    division,
                    granularity = Math.pow(10,(maxLen-1));
                if(maxLen == 1){
                    division = maxFirst;
                }else if(maxLen >1){
                    if(max % granularity != 0){
                        division = maxFirst+1;
                    }else{
                        division = maxFirst;
                    }
                }
                return {
                        div: division,
                        gran: granularity
                    };
            })(dataVal.max);
            /**
            初始化坐标轴
            */
            var canvas = initChartCol(eleId);
            
            /**
            绘制数据柱
            */
            var groupWidth = Math.floor(canvas.width/dataVal.xR.length),
                colWidth = Math.floor(groupWidth/(dataVal.len+2));
            for(var d in dataVal.vals){
                canvas.ct.fillStyle = opt.color[d];
                for(var i in dataVal.vals[d]){
                    var colHeight = Math.floor(dataVal.vals[d][i]/(yR.div*yR.gran)*canvas.height),
                        posTop = canvas.height-colHeight,
                        posLeft = Math.floor(colWidth+groupWidth*i+colWidth*d);
                    canvas.ct.fillRect(posLeft,posTop,colWidth,colHeight);
                }
            };
            
            /**
            绘制x轴标识文字
            */
            for(var xText in dataVal.xR){
                canvas.ct.fillStyle = "#333";
                canvas.ct.textAlign = "middle";
                var textLeft = Math.floor((groupWidth-canvas.ct.measureText(dataVal.xR[xText]).width)/2+xText*groupWidth);
                canvas.ct.fillText(dataVal.xR[xText],textLeft,canvas.height+15);
            };
            
            /**
            绘制y轴标识文字
            */
            for(var i = 0;i<= yR.div;i++){
                canvas.ct.textAlign = "end";
                canvas.ct.fillText(i*yR.gran,(-5),Math.floor(canvas.height*(1-i/yR.div)));
            };
            
            /**
            绘制颜色标识
            */
        }else if(opt.type == 0){
            console.log("绘制占比图，当前版本还不支持饼图，以后再添加")
        }else{
            throw new TypeError("参数的type属性必须是0或者1的number类型的值，1表示绘制趋势图，0表示绘制占比图，当前type属性值的类型是"+typeof opt.type+"，属性值是"+opt.type)
        }
    }
}
