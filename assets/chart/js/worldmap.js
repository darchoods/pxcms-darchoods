// Attributed : http://techslides.com/demos/d3/d3-world-population.html
//queue
(function(){function n(n){function t(){for(;f=a<c.length&&n>p;){var u=a++,t=c[u],r=l.call(t,1);r.push(e(u)),++p,t[0].apply(null,r)}}function e(n){return function(u,l){--p,null==d&&(null!=u?(d=u,a=s=0/0,r()):(c[n]=l,--s?f||t():r()))}}function r(){null!=d?v(d):i?v(d,c):v.apply(null,[d].concat(c))}var o,f,i,c=[],a=0,p=0,s=0,d=null,v=u;return n||(n=1/0),o={defer:function(){return d||(c.push(arguments),++s,t()),o},await:function(n){return v=n,i=!1,s||r(),o},awaitAll:function(n){return v=n,i=!0,s||r(),o}}}function u(){}"undefined"==typeof module?self.queue=n:module.exports=n,n.version="1.0.4";var l=[].slice})();

var width, height, tooltip, path, projection, g, svg;

jQuery(window).ready(function() {
    width = document.getElementById('countries').offsetWidth-60;
    height = width / 2;

    tooltip = d3.select("body").append("div").attr("class", "tip hidden");

    setup(width,height);


    queue()
        .defer(d3.json, "/packages/module/darchoods/assets/chart/js/data/world-110m-cia.json")
        .defer(d3.csv, "/heartbeat/population.csv")
        .await(ready);

});

function setup(width,height){

    projection = d3.geo.mercator().translate([0, 0]).scale(width / 2 / Math.PI);

    path = d3.geo.path().projection(projection);

    svg = d3.select("#countries").append("svg")
        .attr("width", width)
        .attr("height", height);

    var outterg = svg.append("g").attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    g = outterg.append("g").attr("id", "innerg");

}

function addCommas(nStr){
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function ready(error, world, population) {
    //lets sort by population
    var sorted = population.sort(function(a, b){ return d3.descending(parseInt(a.population), parseInt(b.population)); });

    var split = [1, 5, 10, 20, 50, 100];
    var colors = ["#C6DBEF","#9ECAE1","#6BAED6","#4292C6","#2171B5","#08519C","#08306B"];

    var color = d3.scale.threshold()
        .domain(split)
        .range(colors);

    topo = topojson.feature(world, world.objects.countries).features;

    var country = d3.select("#innerg").selectAll(".country").data(topo);

    //ofsets
    var offsetL = document.getElementById('countries').offsetLeft+30;
    var offsetT = document.getElementById('countries').offsetTop-30;

    country.enter().insert("path")
        .attr("class", "country")
        .attr("d", path)
        .attr("id", function(d,i) {
            return d.id;
        })
        .attr("title", function(d,i) {
            return d.properties.name;
        })
        .style("fill", function(d,i) {
            var m = population.filter(function(f){
                return f.country == d.id;
            });
            if (m.length>0) {
                return color(m[0].population);
            }
        })
        .style("stroke", "#fff")
        .on("mousemove", function(d,i) {
            var mouse = d3.mouse(svg.node()).map(function(d) {
                return parseInt(d);
            });

            var pop = '';
            var m = population.filter(function(f){return f.country == d.id});
            if(m.length>0){
                  pop += ' | Population: '+addCommas(m[0].population);
            }

            jQuery('#country').html('<i class="fa fa-info-circle"></i> '+d.properties.name+pop);
        })
        .on("mouseout", function(d,i) {
            jQuery('#country').html('<i class="fa fa-info-circle"></i> Hover over a country');
        });

}