$(function(){
    var month = '';
    var year = '';
    var date = new Date();
    var nowMonth = date.getMonth()+1;
    var nowYear = date.getFullYear();
    for(var i=2017;i<=nowYear;i++){
        if(i == nowYear){
            year += '<option value="'+i+'" selected>'+i+'年</option>';
        }else{
            year += '<option value="'+i+'">'+i+'年</option>';
        }
    }
    for(var i=1;i<=12;i++){
        if(i == nowMonth){
            month += '<option value="'+i+'" selected>'+i+'月</option>';
        }else{
            month += '<option value="'+i+'">'+i+'月</option>';
        }

    }
    $('#year').append(year);
    $('#year2').append(year);
    $('#month').append(month);
    $('#month2').append(month);

    //showpie();
    showLine(nowMonth,nowYear);
    showbar(nowMonth,nowYear);
    showmap();



    $(document).on('change',['#month','#year'],function(){
        $('#morris-line-chart').remove();
        $('.line').append('<div id="morris-line-chart" style="height: 300px;"></div>');
        showLine($('#month option:selected').val(),$('#year option:selected').val());
    })
    $(document).on('change',['#month2','#year2'],function(){
        $('#morris-bar-chart').remove();
        $('.bar').append('<div id="morris-bar-chart" style="height: 300px;"></div>');
        showbar($('#month2 option:selected').val(),$('#year2 option:selected').val());
    })
})
function fetchData(cb,type,month,year){
    $.ajax({
        url:"http://www.tp5.com/admin/Chart/"+type,
        data:{month:month,year:year},
        type:'post',
        dataType:'json',
        success:function(data){
            if(data == '0'){
                alert('没有该月记录');
                return ;
            }else{
                cb({
                    data:data
                });
            }

        }
    })
}


function showpie(){
    var mymess = echarts.init($('#morris-donut-chart').get(0));
    var option = null;
    option = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'right',
            data:''
        },
        series: [
            {
                name:'访问来源',
                type:'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '30',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:''
            }
        ]
    };
    mymess.showLoading();
    fetchData(function(data){

        mymess.hideLoading();
        mymess.setOption({
            legend:{
                data:data.data.type
            },
            series: [{
                data: data.data.data
            }]
        });
    },'getPie',0,0)
    if (option && typeof option === "object") {
        mymess.setOption(option, true);
    }
}


function showLine(month,year){
    var mymess = echarts.init($('#morris-line-chart').get(0));
    var option = null;

    option = {
        xAxis: {
            type: 'category',
            data: ''
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            data: '',
            type: 'line',
            symbol: 'triangle',
            symbolSize: 20,
            lineStyle: {
                normal: {
                    color: 'green',
                    width: 4,
                    type: 'dashed'
                }
            },
            itemStyle: {
                normal: {
                    borderWidth: 3,
                    borderColor: 'yellow',
                    color: 'blue'
                }
            }
        }]
    };
    mymess.showLoading();
    fetchData(function(data){
        mymess.hideLoading();
        mymess.setOption({
            xAxis:{
                data:data.data.categories
            },
            series: [{
                data: data.data.data
            }]
        });
    },'getLine',month,year)
    if (option && typeof option === "object") {
        mymess.setOption(option, true);
    }
}


function showbar(month,year){
    var mymess = echarts.init($('#morris-bar-chart').get(0));
    var option = null;
    option = {
        xAxis: {
            type: 'category',
            data: ''
        },
        yAxis: {
            type: 'value'
        },
        series: ''
    };
    mymess.showLoading();
    fetchData(function(data){
        mymess.hideLoading();
        mymess.setOption({
            xAxis:{
                data:data.data.categories
            },
            series: [{'data':data.data.data,'type':'bar'}]
        });
    },'getBar',month,year)
    if (option && typeof option === "object") {
        mymess.setOption(option, true);
    }

}


function showmap(){
    var mymess = echarts.init($('#morris-area-chart').get(0));
    var option = null;
    var option = {
        title: {
            text: '全国商品销售分布',
            x:'center',
            top: 20
        },
        legend: {
            data:["销售量"]
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c}"
        },
        toolbox: {
            show: true,
            orient : 'vertical',
            left: 'right',
            top: 'center',
            feature : {
                mark : {show: true},
                dataView : {show: false, readOnly: false},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        visualMap: {
            min: 0,
            left: 'left',
            top: 'bottom',
            text:['高','低'],           // 文本，默认为数值文本
            calculable : true,
            orient:'horizontal',
            color:['orangered','yellow','white']
        },
        series: ''
    };
    mymess.showLoading();
    fetchData(function (data) {
        var aa = data.data;
        var num = [];
        var map = initmap();
        $.each(map,function(index,val){
            aa.push({name:val,value:0});
        });
        $.each(aa,function(index,val){
            num.push(val.value);
        })
        mymess.hideLoading();
        mymess.setOption({

            series: [{
                name: option.title.text,
                type: 'map',
                mapType: 'china',
                roam: false,//是否开启鼠标缩放和平移漫游
                showLegendSymbol: false,
                label: {
                    normal: {
                        show: true //是否显示标签。
                    },
                    emphasis: {
                        show: true  //提示标签
                    }
                },
                data:aa
            }],
            visualMap:{
                max:Math.max.apply(null,num)+2
            }
        });
    },'getMap',0,0);
    if (option && typeof option === "object") {
        mymess.setOption(option, true);
    }
}

function initmap(){
    var str ='北京，天津，上海，重庆，河北，山西，辽宁，吉林，黑龙江，江苏，浙江，安徽，福建，江西，山东，河南，湖北，湖南，广东，海南，四川，贵州，云南，陕西，甘肃，青海，台湾，内蒙古，广西，西藏，宁夏，新疆，香港，澳门';
    var arr = str.split('，');
    return arr;
}
