//DOM Interactions
		jQuery(document).ready(function($) {
			var relcont = $('#relations-container');
			var artistcont = $('#single-relations');
			var globalwrapcont = $('.global-relations-wrapper')
			var artistwrapcont = $('.single-relations-wrapper');
			var taxitems = $('#taxitems');
			var taxlinks = $('#taxitems ul li a');

			var cy = cytoscape({
				container: relcont,
				elements: json_obj,
				zoomingEnabled: true,
				maxZoom: 1.2,
                minZoom: 0.4,
                layout: {
                    name: 'spread',
                    animate: true,
                    randomize: true,
                    fit: true,
                    padding:30,
                    minDist: 90,
                    animationEasing: 'ease-in',
                    animationDuration: 500
                }
			});

			var spreadlayout = cy.layout({
				name: 'spread',
				animate: true,
				randomize: true,
				fit: true,
				padding:30,
				minDist: 90,
				animationEasing: 'ease-in',
				animationDuration: 500
            });

            var gridlayout = cy.layout({
				name: 'grid',
				animate: true,
				randomize: true,
				fit: true,
				padding:30,
				minDist: 90,
				animationEasing: 'ease-in',
				animationDuration: 500
            });
            
            var circlelayout = cy.layout({
				name: 'circle',
				animate: true,
                randomize: true,
                radius: 225,
                nodeDimensionsIncludeLabels: false,
				fit: true,
				padding:30,
				minDist: 90,
				animationEasing: 'ease-in',
				animationDuration: 500
			});

			var singlecy = cytoscape({
					container: artistcont,
					zoomingEnabled: true,
					maxZoom: 1.1,
					minZoom: 0.6
				});

			var singlelayout = singlecy.layout({
					name: 'grid',
					animate: true,
					fit: true,
					padding:30,
					//minDist: 60,
					animationEasing: 'ease-in',
					animationDuration: 500
            });
                
            var singlespreadlayout = singlecy.layout({
					name: 'spread',
					animate: true,
                    fit: true,
                    randomize: true,
					padding:30,
					minDist: 90,
					animationEasing: 'ease-in',
					animationDuration: 500
			});

			singlecy.style(generalstyle);

			cy.style(generalstyle);
			cy.add(json_edgeobj[curtax]);
			spreadlayout.run();
			
			var curtaxedges = cy.elements('edge');
			
			cy.on('mouseover', 'node, edge', function(event) {
				taxlinks.removeClass('active');
				cy.$('node').removeClass('hover selected');
			});

			cy.on('mouseover', 'node', function(event) {
				nodeMouseOver(event, cy);
			});

			cy.on('mouseout', 'node', function(event) {
				nodeMouseOut(event, cy);
			});

			cy.on('mouseover', 'edge', function(event) {
				edgeMouseOver(event, cy);
            });
            
            cy.on('click, tap', 'edge', function(event) {
				edgeTap(event, cy);
            });
            
            cy.on('tap', function(event) {
                anyTap(event, cy);
            });

			cy.on('mouseout', 'edge', function(event) {
				edgeMouseOut(event, cy);
			});

			singlecy.on('mouseover', 'node', function(event) {
				nodeMouseOver(event, singlecy);
			});

			singlecy.on('mouseout', 'node', function(event) {
				nodeMouseOut(event, singlecy);
			});

			singlecy.on('mouseover', 'edge', function(event) {
				edgeMouseOver(event, singlecy);
			});

			singlecy.on('mouseout', 'edge', function(event) {
				edgeMouseOut(event, singlecy);
            });
            
            singlecy.on('click, tap', 'edge', function(event) {
				edgeTap(event, singlecy);
            });
            
            singlecy.on('tap', function(event) {
                anyTap(event, singlecy);
            });


			cy.on('click, tap', 'node', function(event) {
				singlecy.$('node').remove();
				globalwrapcont.hide();
				artistwrapcont.show();

				var node = event.target;
				var nodeId = node.id();
				cy.elements('node, edge').removeClass('under hover hoveredge selected nolabels');
				var neighbors = cy.$('node#' + nodeId).closedNeighborhood();
				
                pamPutData(node.data());
                
                $('.relations-info .taxsection').removeClass('active');
                $('.relations-info .taxsection[data-tax="' + curtax + '"]').addClass('active');

				singlecy.add(neighbors);
				singlelayout.run();
				singlecy.center(singlecy.$('node#' + node.id()));
				singlecy.$('node, edge').removeClass('hover');
				singlecy.$('node').addClass('bigger');
				singlecy.$('node#' + nodeId).addClass('selected');
				singlecy.$('node')
				singlecy.resize();
				singlecy.fit();
            });

            singlecy.on('click, tap', 'node', function(event) {
                if(insingle === false) {
                    singlecy.$('node').remove();
                    
                    var node = event.target;
                    var nodeId = node.id();
                    var neighbors = cy.$('node#' + nodeId).closedNeighborhood();
                    
                    pamPutData(node.data());
                    
                    $('.relations-info .taxsection').removeClass('active');
                    $('.relations-info .taxsection[data-tax="' + curtax + '"]').addClass('active');

                    singlecy.add(neighbors);
                    singlelayout.run();
                    singlecy.center(singlecy.$('node#' + node.id()));
                    singlecy.$('node, edge').removeClass('hover');
                    singlecy.$('node').addClass('bigger');
                    singlecy.$('node#' + nodeId).addClass('selected');
                    singlecy.$('node')
                    singlecy.resize();
                    singlecy.fit();
                }
			});
			
			$('.relations-switcher a').on('click', function(e) {
				e.preventDefault;
				if(!$(this).hasClass('active')) {
					curtax = $(this).attr('data-tax');
					var taxlinks = $('#taxitems ul li a');
					$('.relations-switcher a, #taxitems ul.active').removeClass('active');
					$(this).addClass('active');

					$('#taxitems ul[data-tax="' + curtax + '"]').addClass('active');

					cy.elements('node').removeClass('hover selected');
					cy.remove( curtaxedges );
					cy.add(json_edgeobj[curtax]);
					spreadlayout.run();
					curtaxedges = cy.elements('edge');
					taxlinks.removeClass('active');


				}
			});

			$('#taxitems ul li a').on('click', function(e) {
				e.preventDefault;
				if(!$(this).hasClass('active')) {
					var others = $('#taxitems ul li a');
					var tax = $(this).attr('data-tax');
					var taxid = $(this).attr('data-taxid');
					els = cy.elements('node[dt-' + tax + '-' + taxid +']');
					cy.elements('node').removeClass('selected');
					els.addClass('selected');
					others.removeClass('active');
					$(this).addClass('active');
				}
            });
            
            $('.layout-switcher a').on('click', function(e) {
                e.preventDefault;
                var chlay = $(this).attr('data-layout');
                var chclass = $(this).attr('data-class');
                if(chlay === 'circle') {
                    circlelayout.run();
                }
                else if(chlay === 'random') {
                    spreadlayout.run();
                }
                else if(chlay === 'grid') {
                    gridlayout.run();
                }
                else if(chclass === 'nolabels') {
                    cy.elements('node').toggleClass('nolabels');
                }

            });

			artistwrapcont.on('click', 'a.back', function(e) {
				e.preventDefault;

				
				globalwrapcont.show();
				artistwrapcont.hide();
				
				singlelayout.run();
				
				var infob = $('.single-relations-wrapper .mobile-nav a.infomobile');
				if(infob.hasClass('expanded')) {
                    $('.relations-info').removeClass('expanded');
                    infob.removeClass('expanded');
                    infob.text('+ info');
                }
            });
            
            artistwrapcont.on('click', '.taxsection .taxtip', function(e) {
                e.preventDefault;
                var dataId = $(this).attr('data-taxid');
                var dataTax = $(this).attr('data-tax');
                var others = $('#taxitems ul li a');

                artistwrapcont.hide();
                globalwrapcont.show();
                $('.relations-switcher a[data-tax="' + dataTax + '"]').trigger('click');
                
                els = cy.elements('node[dt-' + dataTax + '-' + dataId +']');
				cy.elements('node').removeClass('hover');
                
                els.addClass('selected');
                
                others.removeClass('active');
                var selected =  $('#taxitems ul[data-tax="' + dataTax + '"] li a[data-taxid="' + dataId + '"]'); 
				selected.addClass('active');

				var taxcontainer = $('#taxitems ul.active');
				
				taxcontainer.animate({
					scrollTop: selected.offset().top - taxcontainer.offset().top + taxcontainer.scrollTop()
				});
				
				var infob = $('.single-relations-wrapper .mobile-nav a.infomobile');
				if(infob.hasClass('expanded')) {
                    $('.relations-info').removeClass('expanded');
                    infob.removeClass('expanded');
                    infob.text('+ info');
                }
            });

            $('.single-relations-wrapper').on('click', '.mobile-nav a.infomobile',  function(e) {
                var infob = $(this);
                if(infob.hasClass('expanded')) {
                    $('.relations-info').removeClass('expanded');
                    infob.removeClass('expanded');
                    infob.text('+ info');
                } else {
                    $('.relations-info').addClass('expanded');
                    infob.addClass('expanded');
                    infob.text('- info');
                }
            });

            $('.single-relations-wrapper').on('click', '.relations-info .taxsection h3', function(e) {
                singlecy.$('node').remove();
                singlecy.$('edge').remove();

                curtax = $(this).attr('data-tax');
                nodeId = $(this).attr('data-nodeid');

                var others = $('.single-relations-wrapper .relations-info .taxsection');
				
				$('.relations-switcher a, #taxitems ul.active').removeClass('active');
                
                others.removeClass('active');
                $(this).parent('div').addClass('active');

				$('#taxitems ul[data-tax="' + curtax + '"]').addClass('active');
                $('.relations-switcher a[data-tax="' + curtax + '"]').addClass('active');

				cy.elements('node').removeClass('hover selected');
				cy.remove( curtaxedges );
                cy.add(json_edgeobj[curtax]);
                
                var neighbors = cy.$('node#' + nodeId).closedNeighborhood();
                singlecy.add(neighbors);

				singlespreadlayout.run();
				singlecy.center(singlecy.$('node#' + nodeId));
				singlecy.$('node, edge').removeClass('hover');
				singlecy.$('node#' + nodeId).addClass('selected');
		
				singlecy.resize();
                singlecy.fit();
                
                curtaxedges = cy.elements('edge');
            });

            if(insingle === true) {
                cy.elements('node#' + current_person ).trigger('click');
                singlespreadlayout.run();
                singlecy.center('node#' + current_person);
            }

		});