//Poner el label debajo
sigma.canvas.labels.def = function(node, context, settings) {
    var fontSize,
        prefix = settings('prefix') || '',
        size = node[prefix + 'size'],
        labelWidth = 0,
        labelPlacementX,
        labelPlacementY,
        alignment;

    if (size < settings('labelThreshold'))
      return;

    if (typeof node.label !== 'string')
      return;

    if (settings('labelAlignment') === undefined){ 
      alignment = settings('defaultLabelAlignment');
    } else {
      alignment = settings('labelAlignment');
    }

    fontSize = (settings('labelSize') === 'fixed') ?
      settings('defaultLabelSize') :
      settings('labelSizeRatio') * size;

    context.font = (settings('fontStyle') ? settings('fontStyle') + ' ' : '') +
      fontSize + 'px ' + settings('font');
    context.fillStyle = 'black';

    labelWidth = context.measureText(node.label).width;
    labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
    labelPlacementY = Math.round(node[prefix + 'y'] + fontSize / 3);

    switch (alignment) {
      case 'bottom':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
      default:
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
    }

	  context.beginPath();
      
    context.fillStyle = 'rgba(255,255,255,1)';

    if (node.label && typeof node.label === 'string') {
      w = Math.round(
        context.measureText(node.label).width + fontSize / 2 + size + 7
      );
      h = Math.round(fontSize + 4);
      e = Math.round(fontSize / 2 + 2);
	  var bglabelposY = labelPlacementY - h + 1;

      context.moveTo(labelPlacementX, bglabelposY);
      context.lineTo(labelPlacementX, bglabelposY, labelPlacementX + e, bglabelposY, e);
      context.lineTo(labelPlacementX + w, bglabelposY);
      context.lineTo(labelPlacementX + w, bglabelposY + h);
      context.lineTo(labelPlacementX + e, bglabelposY + h);
      context.lineTo(labelPlacementX, bglabelposY + h, labelPlacementX, bglabelposY + h - e, e);
      context.lineTo(labelPlacementX, bglabelposY + e);

      context.closePath();
      context.fill();

      context.shadowOffsetX = 0;
      context.shadowOffsetY = 0;
      context.shadowBlur = 0;
    }

	  context.closePath();
    context.fill();

	// Node:
    node.color = node.active === true ? node.hovercolor : node.color;

    var nodeRenderer = sigma.canvas.nodes[node.type] || sigma.canvas.nodes.def;
    nodeRenderer(node, context, settings);

    // Display the label:
    if (node.label && typeof node.label === 'string') {
      context.fillStyle = node.active === true ? pamcolors.black :  node.labelcolor;

      context.fillText(
        node.label,
        Math.round(labelPlacementX + size + 3),
        Math.round(labelPlacementY - fontSize / 3.5)
      );
    }


  };

//Hover
sigma.canvas.hovers.def = function(node, context, settings) {
    var fontSize,
        prefix = settings('prefix') || '',
        size = node[prefix + 'size'],
        labelWidth = 0,
        labelPlacementX,
        labelPlacementY,
        alignment;

    if (size < settings('labelThreshold'))
      return;

    if (typeof node.label !== 'string')
      return;

    if (settings('labelAlignment') === undefined){ 
      alignment = settings('defaultLabelAlignment');
    } else {
      alignment = settings('labelAlignment');
    }

    fontSize = 14;

    context.font = (settings('fontStyle') ? settings('fontStyle') + ' ' : '') +
      fontSize + 'px ' + settings('font');
    context.fillStyle = pamcolors.black;

    labelWidth = context.measureText(node.label).width;
    labelPlacementX = Math.round(node[prefix + 'x'] + size + 3);
    labelPlacementY = Math.round(node[prefix + 'y'] + fontSize / 3);

    switch (alignment) {
      case 'bottom':
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
      default:
        labelPlacementX = Math.round(node[prefix + 'x'] - labelWidth / 2 );
        labelPlacementY = labelPlacementY + size + fontSize;
        break;
    }

	context.beginPath();

    context.fillStyle = '#fff';

    if (node.label && typeof node.label === 'string') {
      w = Math.round(
        context.measureText(node.label).width + fontSize / 2 + size + 7
      );
      h = Math.round(fontSize + 4);
      e = Math.round(fontSize / 2 + 2);
	    
      var bglabelposY = labelPlacementY - h + 0.5;

      context.moveTo(labelPlacementX, bglabelposY);
      context.lineTo(labelPlacementX, bglabelposY, labelPlacementX + e, bglabelposY, e);
      context.lineTo(labelPlacementX + w, bglabelposY);
      context.lineTo(labelPlacementX + w, bglabelposY + h);
      context.lineTo(labelPlacementX + e, bglabelposY + h);
      context.lineTo(labelPlacementX, bglabelposY + h, labelPlacementX, bglabelposY + h - e, e);
      context.lineTo(labelPlacementX, bglabelposY + e);

      context.closePath();
      context.fill();

      context.shadowOffsetX = 0;
      context.shadowOffsetY = 0;
      context.shadowBlur = 0;
    }

	  context.closePath();
    context.fill();

	// Node:
    var nodeRenderer = sigma.canvas.nodes[node.type] || sigma.canvas.nodes.def;
  
    node.color = node.hovercolor;

    nodeRenderer(node, context, settings);
    

    // Display the label:
    if (node.label && typeof node.label === 'string') {
      context.fillStyle = pamcolors.black;

      context.fillText(
        node.label,
        Math.round(labelPlacementX + size + 3),
        Math.round(labelPlacementY - fontSize / 3.5)
      );
    }

    



  };
