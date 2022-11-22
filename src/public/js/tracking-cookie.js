
import Cookies from 'js-cookie';

const AlquemieJS = {
	getUrlParameter: function (name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	}
};

alquemie.LastCamp = Cookies.getJSON('aqcamplast');
alquemie.Campaign = Cookies.getJSON('aqcamp');

if (AlquemieJS.getUrlParameter(alquemie.QS.source) != '') {
	alquemie.LastCamp = {
		"campaign": AlquemieJS.getUrlParameter(alquemie.QS.campaign).toLowerCase(),
		"source": AlquemieJS.getUrlParameter(alquemie.QS.source).toLowerCase(),
		"medium": AlquemieJS.getUrlParameter(alquemie.QS.medium).toLowerCase(),
		"term": AlquemieJS.getUrlParameter(alquemie.QS.term).toLowerCase(),
		"content": AlquemieJS.getUrlParameter(alquemie.QS.content).toLowerCase()
	};
} else if (AlquemieJS.getUrlParameter('utm_source') != '') {
	alquemie.LastCamp = {
		"campaign": AlquemieJS.getUrlParameter('utm_campaign').toLowerCase(),
		"source": AlquemieJS.getUrlParameter('utm_source').toLowerCase(),
		"medium": AlquemieJS.getUrlParameter('utm_medium').toLowerCase(),
		"term": AlquemieJS.getUrlParameter('utm_term').toLowerCase(),
		"content": AlquemieJS.getUrlParameter('utm_content').toLowerCase()
	};
} else if (typeof alquemie.LastCamp == 'undefined') {
	var source = campaign = '';
	try {
		if (typeof document.referrer != 'undefined') {
			var a=document.createElement('a');
			a.href = document.referrer;
		}
		if (a.hostname != location.hostname) {
			source = a.hostname;
			campaign = 'seo';
		}

	} catch(e) {
		console.log(e.message);
	}

	alquemie.LastCamp = {
		"campaign": campaign,
		"source": source.toLowerCase(),
		"medium": "",
		"term": "",
		"content": ""
	};
}

var mtype = AlquemieJS.getUrlParameter(alquemie.QS.matchtype);
if (mtype != '' || (typeof alquemie.LastCamp.matchtype == 'undefined')) alquemie.LastCamp.matchtype = mtype;

var gclid = AlquemieJS.getUrlParameter('gclid');
if (gclid != '' || (typeof alquemie.LastCamp.gclid == 'undefined')) alquemie.LastCamp.gclid = gclid;

if (typeof alquemie.Campaign == 'undefined') {
	alquemie.Campaign = alquemie.LastCamp;
}

Cookies.withAttributes({ path: '/', expires: alquemieCookieLife, secure: true, sameSite: 'Lax'  })
Cookies.set('aqcamplast', alquemie.LastCamp);
Cookies.set('aqcamp', alquemie.Campaign, { expires: alquemieCookieLife });

alquemie.attribution = (typeof alquemie.attribution == 'undefined') ? 'last' : alquemie.attribution;

if (alquemie.attribution == 'first') {
	alquemie.thisCampaign = alquemie.Campaign;
} else {
	alquemie.thisCampaign = alquemie.LastCamp;
}
if (typeof dataLayer != 'undefined') dataLayer.push(alquemie.thisCampaign);

function updateCampaignFields() {
	if (jQuery(("input[data-alquemie='campaign']")) != null) {
		jQuery(("input[data-alquemie='campaign']")).val(JSON.stringify(alquemie.thisCampaign));
	}
}

document.addEventListener("DOMContentLoaded",function(event) {
		updateCampaignFields();
});
var gforms = document.getElementsByClassName("gform_wrapper");
for (var f = 0; f < gforms.length; f++) {
	gforms[f].addEventListener("DOMSubtreeModified", function(event) {
		updateCampaignFields();
	});
}

if (typeof ga != 'undefined') {
	ga(function(tracker) {
		var clientId = tracker.get('clientId');
		console.log('ClientID: ' + clientId);
	});
}