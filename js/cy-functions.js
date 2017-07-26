var pamcolors = {
    black: '#000',
    red: '#ff0000',
    gray: '#808080',
    lightgray: '#ccc',
    white: '#ffffff'
}

function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}

function pamPutData(data) {
	var container = jQuery('.single-relations-wrapper');
	var ficha = jQuery('.relations-info', container);
	
	var fichatemplate = jQuery('#relations-template').html();
	Mustache.parse(fichatemplate);
	var rendered_content = Mustache.render(fichatemplate, data);
	jQuery('.content', ficha).empty().append(rendered_content);
}

function nodeMouseOver(event, instance) {
    var node = event.target;
    var nodeId = node.id();
    var neighbors = instance.$('node#' + nodeId).closedNeighborhood();
    neighbors.map(function(neighnode) {
        instance.$('#' + neighnode.id()).addClass('hover');	
    });
}

function nodeMouseOut(event, instance) {
    var node = event.target;
    var nodeId = node.id();
    var neighbors = instance.$('node#' + nodeId).closedNeighborhood();
    neighbors.map(function(neighnode) {
        instance.$('#' + neighnode.id()).removeClass('hover');	
    });
}

function edgeMouseOver(event, instance) {
    var edge = event.target;
    var edgeId = edge.id();
    var edgeSource = edge.source();
    var edgeTarget = edge.target();
    
    instance.$('edge#' + edgeId).addClass('hoveredge');
    instance.$('node, edge').addClass('under');
    edge.connectedNodes().removeClass('under');
    instance.$('edge#' + edgeId).removeClass('under');
    instance.$('node#' + edgeSource.id() + ', ' + 'node#' + edgeTarget.id()).addClass('hover');
}

function edgeTap(event, instance) {
    var edge = event.target;
    var edgeId = edge.id();
    var edgeSource = edge.source();
    var edgeTarget = edge.target();
    instance.elements('edge, node').removeClass('hover under hoveredge selected');
    
    instance.$('edge#' + edgeId).addClass('hoveredge');
    instance.$('node, edge').addClass('under');
    edge.connectedNodes().removeClass('under');
    instance.$('edge#' + edgeId).removeClass('under');
    instance.$('node#' + edgeSource.id() + ', ' + 'node#' + edgeTarget.id()).addClass('hover');
}

function anyTap(event, instance) {
    if(event.target === instance) {
        instance.$('edge, node').removeClass('hoveredge under hover selected');
    }
}

function edgeMouseOut(event, instance) {
    var edge = event.target;
    var edgeId = edge.id();
    var edgeSource = edge.source();
    var edgeTarget = edge.target();
    instance.$('node').removeClass('under');
    instance.$('edge#' + edgeId).removeClass('hoveredge');
    instance.$('node#' + edgeSource.id() + ', ' + 'node#' + edgeTarget.id()).removeClass('hover');
}