function Tool(elementId){

	var $el = jQuery(elementId),
		$opts = jQuery('<div/>').css('width', '100%').appendTo($el),
		$input = jQuery('<input type="text" class="date" value="2009-03-16T00:00:00"/>')
			.css({
				'padding': '4px 0 6px 20px',
			}).appendTo($opts),
		$go = jQuery('<button class="btn btn-default">Go</button>').appendTo($opts),
		$map = jQuery('<div id="map"/>').css({
			'height' : '400px',
			'width' : '100%',
			'max-width' : '1200px',
			'margin' : '0 auto'
		}).appendTo($el);

	var map = new ol.Map({
		target: 'map',
		layers: [
			new ol.layer.Tile({
				source: new ol.source.TileWMS({
					url: 'http://wdc.dlr.de/cgi-bin/geo',
					params: {'LAYERS' : 'world_boundaries'}
				})
			}),
		],
		view: new ol.View({
			center: [37.41, 8.82],
			projection: 'EPSG:4326',
			zoom: 2
		})
	});

	var nasaLayer = {};

	var go = function(){
		map.removeLayer(nasaLayer);
		nasaLayer = new ol.layer.Image({
			extent: [-281.25, -140.625, 281.25, 140.625],
			source: new ol.source.ImageWMS({
				url: 'http://gdata2.sci.gsfc.nasa.gov/daac-bin/wms_acp_3',
				params: {
					'LAYERS' : 'OMTO3e.003::ColumnAmountO3:min=-5:max=5*GOME2_O3.GDP-4::Band1:min=-5:max=5',
					'TRANSPARENT' : 'true',
					'TIME' : $input.val(),
					'STYLES' : 'latlonplot_diff:ctype=custom',
					'SLD' : 'http://wdc.dlr.de/acp/sld/acp44_wdc.xml',
					'FORMAT' : 'image/png',
					'VERSION' : '1.1.1',
					'WIDTH' : '800',
					'HEIGHT' : '400',
					'ratio' : 1
				}
			})
		});
		map.addLayer(nasaLayer);
	};
	$go.click(go);
	go();
}
