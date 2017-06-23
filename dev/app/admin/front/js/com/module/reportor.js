require("scriptjs")("//cdn.bootcss.com/d3/3.5.16/d3.min.js", function() {
    var dataset = [];
    var lines = []; //保存折线图对象
    var xMarks = [];
    var lineNames = []; //保存系列名称
    var lineColor = ["#F00","#09F","#0F0","#333","#F0F"];
    var w = 850;
    var h = 400;
    var padding = 40;
    var currentLineNum = 0;

    //用一个变量存储标题和副标题的高度，如果没有标题什么的，就为0
    var head_height = 30;
    var title = "每日数据统计图";
    var subTitle = "至";

    //用一个变量计算底部的高度，如果不是多系列，就为0
    var foot_height = padding;

    //模拟数据
    getData();
    subTitle  =  xMarks[0]+subTitle+xMarks[xMarks.length-1];



    //判断是否多维数组，如果不是，则转为多维数组，这些处理是为了处理外部传递的参数设置的，现在数据标准，没什么用
    if (!(dataset[0] instanceof Array))
    {
        var tempArr  =  [];
        tempArr.push(dataset);
        dataset  =  tempArr;
    }

    //保存数组长度，也就是系列的个数
    currentLineNum  =  dataset.length;



    //图例的预留位置
    foot_height+= 25;

    //定义画布
    var svg = d3.select("body").select(".reportor")
    .append("svg")
    .attr("width",w)
    .attr("height",h);

    //添加背景
    svg.append("g")
    .append("rect")
    .attr("x",0)
    .attr("y",0)
    .attr("width",w)
    .attr("height",h)
    .style("fill","#FFF")
    .style("stroke-width",2)
    .style("stroke","#E7E7E7");

    //添加标题
    if (title!= "")
    {
        svg.append("g")
        .append("text")
        .text(title)
        .attr("class","title")
        .attr("x",w/2)
        .attr("y",head_height);

        head_height+= 30;
    }

    //添加副标题
    if (subTitle!="")
    {
        svg.append("g")
        .append("text")
        .text(subTitle)
        .attr("class","subTitle")
        .attr("x",w/2)
        .attr("y",head_height);

        head_height+= 20;
    }

    maxdata = getMaxdata(dataset);

    //横坐标轴比例尺
    var xScale = d3.scale.linear()
        .domain([0,dataset[0].length-1])
        .range([padding,w-padding]);

    //纵坐标轴比例尺
    var yScale = d3.scale.linear()
        .domain([0,maxdata])
        .range([h-foot_height,head_height]);

    //定义横轴网格线
    var xInner = d3.svg.axis()
            .scale(xScale)
            .tickSize(-(h-head_height-foot_height),0,0)
            .tickFormat("")
            .orient("bottom")
            .ticks(dataset[0].length);

    //添加横轴网格线
    var xInnerBar = svg.append("g")
            .attr("class","inner_line")
            .attr("transform", "translate(0," + (h - padding) + ")")
            .call(xInner);

    //定义纵轴网格线
    var yInner = d3.svg.axis()
            .scale(yScale)
            .tickSize(-(w-padding*2),0,0)
            .tickFormat("")
            .orient("left")
            .ticks(10);

    //添加纵轴网格线
    var yInnerBar = svg.append("g")
            .attr("class", "inner_line")
            .attr("transform", "translate("+padding+",0)")
            .call(yInner);

    //定义横轴
    var xAxis = d3.svg.axis()
            .scale(xScale)
            .orient("bottom")
            .ticks(dataset[0].length);

    //添加横坐标轴
    var xBar = svg.append("g")
            .attr("class","axis")
            .attr("transform", "translate(0," + (h - foot_height) + ")")
            .call(xAxis);
    //通过编号获取对应的横轴标签
    xBar.selectAll("text")
        .text(function(d){
            if(d%7 == 0)
                return xMarks[d];
        });

    //定义纵轴
    var yAxis = d3.svg.axis()
            .scale(yScale)
            .orient("left")
            .ticks(10);

    //添加纵轴
    var yBar = svg.append("g")
            .attr("class", "axis")
            .attr("transform", "translate("+padding+",0)")
            .call(yAxis);

    //添加图例
    var legend = svg.append("g");

    addLegend();

    //添加折线
    lines = [];
    for(i = 0;i<currentLineNum;i++)
    {
        var newLine = new CrystalLineObject();
        newLine.init(i);
        lines.push(newLine);
    }


    //定义折线类
    function CrystalLineObject()
    {
        this.group = null;
        this.path = null;
        this.oldData = [];
        var tooltip = d3.select("body")
                            .append("div")
                            .attr("class","tooltip")
                            .style("opacity",0.0);


        this.init = function(id)
        {
            var arr = dataset[id];
            this.group = svg.append("g");

            var line = d3.svg.line()
                .x(function(d,i){return xScale(i);})
                .y(function(d){return yScale(d);});

            //添加折线
            this.path = this.group.append("path")
                .attr("d",line(arr))
                .style("fill","none")
                .style("stroke-width",2)
                .style("stroke",lineColor[id])
                .style("stroke-opacity",0.9);

            //添加系列的小圆点
            this.group.selectAll("circle")
            .data(arr)
            .enter()
            .append("circle")
            .attr("cx", function(d,i) {
                    return xScale(i);
            })
            .attr("cy", function(d) {
                    return yScale(d);
            })
            .attr("r",5)
            .attr("fill",lineColor[id])
            .on("click",function(d,i){

                 tooltip.html( xMarks[i]+"增长统计<br/>" +lineNames[0]+": "+dataset[0][i]+"<br/>"
                     +lineNames[1]+": "+dataset[1][i]+"<br/>"
                     +lineNames[2]+": "+dataset[2][i]+"<br/>"
                     +lineNames[3]+": "+dataset[3][i]+"<br/>"
                     +lineNames[4]+": "+dataset[4][i]  )
                    .style("left", (d3.event.pageX) + "px")
                    .style("top", (d3.event.pageY + 10) + "px")
                    .style("opacity",1.0);

               })
            .on("mouseover",function(d,i){

                 tooltip.html( lineNames[id]+"增长值:"+d + "<br/>" + "日期:"+xMarks[i] )
                    .style("left", (d3.event.pageX) + "px")
                    .style("top", (d3.event.pageY + 20) + "px")
                    .style("opacity",1.0);

               })
            .on("mouseout",function(d){
                /* 鼠标移出时，将透明度设定为0.0（完全透明）*/

                tooltip.style("opacity",0.0);
            });
            this.oldData = arr;
        };





    }

    //添加图例
    function addLegend()
    {
        var textGroup = legend.selectAll("text")
            .data(lineNames);

        textGroup.exit().remove();

        legend.selectAll("text")
            .data(lineNames)
            .enter()
            .append("text")
            .text(function(d){return d;})
            .attr("class","legend")
            .attr("x", function(d,i) {return i*100;})
            .attr("y",0)
            .attr("fill",function(d,i){ return lineColor[i];});

        var rectGroup = legend.selectAll("rect")
            .data(lineNames);

        rectGroup.exit().remove();

        legend.selectAll("rect")
            .data(lineNames)
            .enter()
            .append("rect")
            .attr("x", function(d,i) {return i*100-20;})
            .attr("y",-10)
            .attr("width",12)
            .attr("height",12)
            .attr("fill",function(d,i){ return lineColor[i];});

        legend.attr("transform","translate("+((w-lineNames.length*100)/2)+","+(h-10)+")");
    }

    //产生随机数据
    function getData()
    {

        oldData = dataset;
        dataset = [];
        xMarks = [];
        lineNames = [];

        var rqt_count = [];
        var product_count = [];
        var idea_count = [];
        var feature_count = [];
        var invent_count = [];
        var all_count = [];

        for(var item in data)
        {
            var tmp = data[item];
            rqt_count.push( parseInt(tmp['rqt_count']) );
            product_count.push( parseInt(tmp['product_count']) );
            idea_count.push( parseInt(tmp['idea_count']) );
            feature_count.push( parseInt(tmp['feature_count']) );
            xMarks.push(tmp['date'].substr(5));
            all_count.push( parseInt(tmp['rqt_count'])+parseInt(tmp['product_count'])+parseInt(tmp['idea_count'])+parseInt(tmp['feature_count']) );
        }

        dataset.push(rqt_count);
        lineNames.push("需求");
        dataset.push(product_count);
        lineNames.push("产品");
        dataset.push(idea_count);
        lineNames.push("idea");
        dataset.push( feature_count );
        lineNames.push("特点");
        dataset.push( all_count );
        lineNames.push("总数");

    }

    //取得多维数组最大值
    function getMaxdata(arr)
    {
        maxdata = 0;
        for(i = 0;i<arr.length;i++)
        {
            maxdata = d3.max([maxdata,d3.max(arr[i])]);
        }
        return maxdata;
    }
});
