window.onload = function(){
	initialize();
}

$(document).ready(function() {

	var siteid = $('#tx_staddressmap_addresslist_pageid').html();
	var ajaxtypenumb = $('#tx_staddressmap_addresslist_ajaxtypenumb').html();

	$('.tx_staddressmap_select').change(function(){

		// if select radius -> return
		if($(this).prop('id') == 'tx_staddressmap_select_radiusselect') {
			return;
		}

		var tablefield = this.id.split('_');

		$.get('index.php?id='+siteid+'&type='+ajaxtypenumb+'&ts='+Date.parse(new Date()) + new Date().getMilliseconds(),{
			cid: 	$('#tx_staddressmap_cid').val(),
			t: 		tablefield[3],
			v: 		$('#'+this.id).val()
		},
		function(data){
			$('#tx_staddressmap_addresslist_'+$('#tx_staddressmap_cid').val()).html(data);
			show_marker(0);
		}); 

		
		$('.tx_staddressmap_select').not(this).each(
			function(index,element) {
				element.selectedIndex = 0;
			}
		);

		$('.tx_staddressmap_input').val('');
		
	});

	$('.tx_staddressmap_input').keypress(function(e){
		if(e.which == 13){
			var tablefield = this.id.split('_');

			$.get('index.php?id='+siteid+'&type='+ajaxtypenumb+'&ts='+Date.parse(new Date()) + new Date().getMilliseconds(),{
				cid: 	$('#tx_staddressmap_cid').val(),
				t: 		tablefield[3],
				v: 		$('#'+this.id).val()
			},
			function(data){
				$('#tx_staddressmap_addresslist_'+$('#tx_staddressmap_cid').val()).html(data);
				show_marker(0);
			}); 

			
			$('.tx_staddressmap_select').not(this).each(
				function(index,element) {
					element.selectedIndex = 0;
				}
			);

			$('.tx_staddressmap_select').each(
				function(index,element) {
					element.selectedIndex = 0;
				}
			);
		}
	});
	
	$('.tx_staddressmap_input').focus(function() {
		$('.tx_staddressmap_input').not(this).val('');
		$('.tx_staddressmap_select').each(
			function(index,element) {
				element.selectedIndex = 0;
			}
		);
	})
	
	if($('#tx_staddressmap_seeatstart').val() == 1) {
		$.get('index.php?id='+siteid+'&type='+ajaxtypenumb+'&ts='+Date.parse(new Date()) + new Date().getMilliseconds(),{
			cid: 	$('#tx_staddressmap_cid').val(),
			t: 		'1',
			v: 		$('#'+this.id).val(),
			all:	1,
			order: $('#tx_staddressmap_order').val()
		},
		function(data){
			$('#tx_staddressmap_addresslist_'+$('#tx_staddressmap_cid').val()).html(data);
			setTimeout(function(){ show_marker(0); },500);
		}); 
	}

	if($('.tx_staddressmap_submit').length > 0) {
		$('.tx_staddressmap_submit').click(function() {
			tablefield = $('.tx_staddressmap_input[value!=""]').attr('id').split('_');
			$.get('index.php?id='+siteid+'&type='+ajaxtypenumb+'&ts='+Date.parse(new Date()) + new Date().getMilliseconds(),{
				cid: 	$('#tx_staddressmap_cid').val(),
				t: 		tablefield[3],
				v: 		$('.tx_staddressmap_input[value!=""]').val(),
				rad:	$('#tx_staddressmap_select_radiusselect').val()
			},
			function(data){
				$('#tx_staddressmap_addresslist_'+$('#tx_staddressmap_cid').val()).html(data);
				show_marker(0);
			}); 

			$('.tx_staddressmap_select').not(this).not('#tx_staddressmap_select_radiusselect').each(
				function(index,element) {
					element.selectedIndex = 0;
				}
			);

			$('.tx_staddressmap_select').not('#tx_staddressmap_select_radiusselect').each(
				function(index,element) {
					element.selectedIndex = 0;
				}
			);
		});
	}



	function is_array(value) {
		if (typeof value === 'object' && value && value instanceof Array) {
			return true;
		}
		return false;
	}

	function str_replace(s, r, c) {
		if (is_array(s)) {
			for(i=0; i < s.length; i++) {
				c = c.split(s[i]).join(r[i]);
			}
		}
		else {
			c = c.split(s).join(r);
		}
		return c;
	}

	var infowindow;
	(function(){
		google.maps.Map.prototype.markers = new Array();

		google.maps.Map.prototype.addMarker = function(marker){
			this.markers[this.markers.length] = marker;
		};

		google.maps.Map.prototype.getMarkers = function(){
			return this.markers
		};

		google.maps.Map.prototype.clearMarkers = function(){
			if (infowindow) {
				infowindow.close();
			}

			for (var i = 0; i < this.markers.length; i++) {
				this.markers[i].setMap(null);
			}
		};
	})();

	function createMarker(name, latlng){
		name = str_replace(new Array("|-|","-|-","tx_addressmap_replace"), new Array("'",'"','<a'), name);
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			icon: icon
		});
		google.maps.event.addListener(marker, "click", function(){
			if (infowindow)
				infowindow.close();
			infowindow = new google.maps.InfoWindow({
				maxWidth: 800,
				content: name
			});
			infowindow.open(map, marker);
		});
		return marker;
	}

	function show_marker(id){
		if(id=='-1') {
			return;
		}
		map.clearMarkers();
		if(circle != null) circle.setMap(null);
		if(circledata) circle = new google.maps.Circle(circledata);

		if(marker.length > 0) {
			map.setCenter(new google.maps.LatLng(centerpoints[id].lat, centerpoints[id].lng));
			map.setZoom(detailzoom[id]);
			for (var i = 0; i < marker[id].length; i++) {
				var latlng = new google.maps.LatLng(marker[id][i].lat, marker[id][i].lng);
				map.addMarker(createMarker(marker[id][i].name, latlng));
				while (map.getBounds().contains(latlng) == false) map.setZoom(--detailzoom[id]);
			}
		}
	}

});



