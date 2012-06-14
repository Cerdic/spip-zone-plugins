/*
 * L.Geocoder is used to make geocoding or reverse geocoding requests.
 */

L.Geocoder = L.Class.extend({

	includes: L.Mixin.Events,

	options: {
		forwardUrl: 'http://open.mapquestapi.com/nominatim/v1/search',
		reverseUrl: 'http://open.mapquestapi.com/nominatim/v1/reverse',
		limit: 1,
		addressdetails: 1
	},

	initialize: function (callback, options) {
		L.Util.setOptions(this, options);
		this._user_callback = callback;
	},

	geocode: function (data) {
		if (L.LatLng && (data instanceof L.LatLng)) {
			this._reverse_geocode(data);
		} else if (typeof(data) == 'string') {
			this._geocode(data);
		}
	},

	_geocode: function (text) {
		this._request(
			this.options.forwardUrl,
			{
				format: 'json',
				q: text,
				limit: this.options.limit,
				addressdetails: this.options.addressdetails
			}
		);
	},

	_reverse_geocode: function (latlng) {
		this._request(
			this.options.reverseUrl,
			{
				format: 'json',
				lat: latlng.lat,
				lon: latlng.lng
			}
		);
	},

	_request: function (url, data) {
		console.log(data);
		$.ajax({
			//cache: true,
			context: this,
			data: data,
			dataType: 'jsonp',
			jsonp: 'json_callback',
			success: this._callback,
			url: url
		});
	},
	
	_callback: function (response) {
		var return_location = {};
	console.log(response);
	console.log(typeof(response));
		if (response instanceof Array && !response.length) {
			return false;
		} else {
			return_location.street = '';
			return_location.postcode = '';
			return_location.locality = '';
			return_location.region = '';
			return_location.country = '';
			
			if (response.length > 0) {
				place = response[0];
			} else {
				place = response;
			}
			var street_components = [];
		console.log(place);
			if (place.address.country) {
				return_location.country = place.address.country;
			}
			if (place.address.state) {
				return_location.region = place.address.state;
			}
			if (place.address.city) {
				return_location.locality = place.address.city;
			}
			if (place.address.postcode) {
				return_location.postcode = place.address.postcode;
			}
			if (place.address.road) {
				street_components.push(place.address.road);
			}
			if (place.address.house_number) {
				street_components.unshift(place.address.house_number);
			}
			
			if (return_location.street === '' && street_components.length > 0) {
				return_location.street = street_components.join(' ');
			}
			
			return_location.point = new L.LatLng(place.lat, place.lon);
			
			this._user_callback(return_location);
		}
	},
});