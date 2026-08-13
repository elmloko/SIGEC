<script>
    jQuery(document).ready(function() {
        $("#entidad").jOrgChart({
            chartElement: '#chart',
            dragAndDrop: true
        });
    });
</script>
<style type="text/css">
    ul#entidad {
        margin: inherit;
        padding: inherit;
    }
    
    ul#entidad li {
        margin-left: 20px;
        line-height: 16px;
    }
    
    ul#entidad>li>ul>li {
        margin-left: 20px;
    }
    
    .node a {
        color: #fff;
        font-size: 10px;
        text-transform: uppercase;
    }
    
    #render {
        background: #ccc;
    }
    /* Custom chart styling */
    
    .jOrgChart {
        margin: 10px;
        padding: 5px;
        overflow-x: scroll;
    }
    /* Custom node styling */
    
    .jOrgChart .node {
        font-size: 14px;
        border-radius: 8px;
        border: 3px solid white;
        color: #F38630;
        -moz-border-radius: 5px;
        vertical-align: bottom;
    }
    
    .jOrgChart .node .node {
        font-size: 14px;
        background-color: #666;
        border-radius: 8px;
        border: 5px solid white;
        color: #F38630;
        -moz-border-radius: 8px;
    }
    
    .node p {
        font-family: tahoma;
        font-size: 10px;
        line-height: 11px;
        padding: 2px;
    }
</style>

<?php
echo $lista;
?>
<div id="chart" class="orgChart"></div>