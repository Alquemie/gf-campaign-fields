const AqGfCampaignData = {
	getUrlParameter: function (name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	},
	setCookie: function (cname, cvalue) {
			var d = new Date();
			d.setTime(d.getTime() + (30*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	},
	getCookie: function (cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}

		return '';
	}
};

var AqCampaign = '';
var AqSource =  '';
var AqMedium = '';
var AqTerm = '';
var AqContent = '';
var AqMatchType = '';
var AqMKWID = '';
var AqGCLID = '';

if (AqAttribution == 'first') {
	//Check if Cookie
	AqCampaign = (AqGfCampaignData.getCookie('aq_campaign') != '') ? AqGfCampaignData.getCookie('aq_campaign') : '';
	AqSource = (AqGfCampaignData.getCookie('aq_source') != '') ? AqGfCampaignData.getCookie('aq_source') : '';
	AqMedium = (AqGfCampaignData.getCookie('aq_medium') != '') ? AqGfCampaignData.getCookie('aq_medium') : '';
	AqTerm = (AqGfCampaignData.getCookie('aq_term') != '') ? AqGfCampaignData.getCookie('aq_term') : '';
	AqContent = (AqGfCampaignData.getCookie('aq_content') != '') ? AqGfCampaignData.getCookie('aq_content') : '';
	AqMKWID = (AqGfCampaignData.getCookie('aq_mkwid') != '') ? AqGfCampaignData.getCookie('aq_mkwid') : '';
	AqPCRID = (AqGfCampaignData.getCookie('aq_pcrid') != '') ? AqGfCampaignData.getCookie('aq_pcrid') : '';
	AqMatchType = (AqGfCampaignData.getCookie('aq_matchtype') != '') ? AqGfCampaignData.getCookie('aq_matchtype') : '';
	AqGCLID = (AqGfCampaignData.getCookie('aq_gclid') != '') ? AqGfCampaignData.getCookie('aq_gclid') : '';
}

if (AqCampaign == '') { AqCampaign = (AqGfCampaignData.getUrlParameter(AqCampaignQS) != '') ? AqGfCampaignData.getUrlParameter(AqCampaignQS) : ''; }
if (AqSource == '') { AqSource = (AqGfCampaignData.getUrlParameter(AqSourceQS) != '') ? AqGfCampaignData.getUrlParameter(AqSourceQS) : ''; }
if (AqMedium == '') { AqMedium = (AqGfCampaignData.getUrlParameter(AqMediumQS) != '') ? AqGfCampaignData.getUrlParameter(AqMediumQS) : ''; }
if (AqTerm == '') { AqTerm = (AqGfCampaignData.getUrlParameter(AqTermQS) != '') ? AqGfCampaignData.getUrlParameter(AqTermQS) : ''; }
if (AqContent == '') { AqContent = (AqGfCampaignData.getUrlParameter(AqContentQS) != '') ? AqGfCampaignData.getUrlParameter(AqContentQS) : ''; }
if (AqMKWID == '') { AqMKWID = (AqGfCampaignData.getUrlParameter(AqMKWIDQS) != '') ? AqGfCampaignData.getUrlParameter(AqMKWIDQS) : ''; }
if (AqPCRID == '') { AqMKWID = (AqGfCampaignData.getUrlParameter(AqPCRIDQS) != '') ? AqGfCampaignData.getUrlParameter(AqPCRIDQS) : ''; }
if (AqMatchType == '') { AqMatchType = (AqGfCampaignData.getUrlParameter(AqMatchTypeQS) != '') ? AqGfCampaignData.getUrlParameter(AqMatchTypeQS) : ''; }
if (AqGCLID == '') { AqGCLID = (AqGfCampaignData.getUrlParameter('gclid') != '') ? AqGfCampaignData.getUrlParameter('gclid') : ''; }

if (AqCampaign == '') { AqCampaign = (AqGfCampaignData.getUrlParameter('utm_campaign') != '') ? AqGfCampaignData.getUrlParameter('utm_campaign') : ''; }
if (AqSource == '') { AqSource = (AqGfCampaignData.getUrlParameter('utm_source') != '') ? AqGfCampaignData.getUrlParameter('utm_source') : ''; }
if (AqMedium == '') { AqMedium = (AqGfCampaignData.getUrlParameter('utm_medium') != '') ? AqGfCampaignData.getUrlParameter('utm_medium') : ''; }
if (AqTerm == '') { AqTerm = (AqGfCampaignData.getUrlParameter('utm_term') != '') ? AqGfCampaignData.getUrlParameter('utm_term') : ''; }
if (AqContent == '') { AqContent = (AqGfCampaignData.getUrlParameter('utm_content') != '') ? AqGfCampaignData.getUrlParameter('utm_content') : ''; }

if (AqAttribution != 'first') {
	//Check if Cookie
	if (AqCampaign == '') { AqCampaign = (AqGfCampaignData.getCookie('aq_campaign') != '') ? AqGfCampaignData.getCookie('aq_campaign') : ''; }
	if (AqSource == '') { AqSource = (AqGfCampaignData.getCookie('aq_source') != '') ? AqGfCampaignData.getCookie('aq_source') : ''; }
	if (AqMedium == '') { AqMedium = (AqGfCampaignData.getCookie('aq_medium') != '') ? AqGfCampaignData.getCookie('aq_medium') : ''; }
	if (AqTerm == '') { AqTerm = (AqGfCampaignData.getCookie('aq_term') != '') ? AqGfCampaignData.getCookie('aq_term') : ''; }
	if (AqContent == '') { AqContent = (AqGfCampaignData.getCookie('aq_content') != '') ? AqGfCampaignData.getCookie('aq_content') : ''; }
	if (AqMKWID == '') { AqMKWID = (AqGfCampaignData.getCookie('aq_mkwid') != '') ? AqGfCampaignData.getCookie('aq_mkwid') : ''; }
	if (AqPCRID == '') { AqPCRID = (AqGfCampaignData.getCookie('aq_pcrid') != '') ? AqGfCampaignData.getCookie('aq_pcrid') : ''; }
	if (AqMatchType == '') { AqMatchType = (AqGfCampaignData.getCookie('aq_matchtype') != '') ? AqGfCampaignData.getCookie('aq_matchtype') : ''; }
	if (AqGCLID == '') { AqGCLID = (AqGfCampaignData.getCookie('aq_gclid') != '') ? AqGfCampaignData.getCookie('aq_gclid') : ''; }
}

if (AqCampaign != '') { AqGfCampaignData.setCookie('aq_campaign', AqCampaign); }
if (AqSource != '') { AqGfCampaignData.setCookie('aq_source', AqSource); }
if (AqMedium != '') { AqGfCampaignData.setCookie('aq_medium', AqMedium); }
if (AqTerm != '') { AqGfCampaignData.setCookie('aq_term', AqTerm); }
if (AqContent != '') { AqGfCampaignData.setCookie('aq_content', AqContent); }
if (AqMKWID != '') { AqGfCampaignData.setCookie('aq_mkwid', AqMKWID); }
if (AqPCRID != '') { AqGfCampaignData.setCookie('aq_pcrid', AqPCRID); }
if (AqMatchType != '') { AqGfCampaignData.setCookie('aq_matchtype', AqMatchType);  }
if (AqGCLID != '') { AqGfCampaignData.setCookie('aq_gclid', AqGCLID); }

console.log( document.URL.substr(0, document.URL.lastIndexOf('/')) );
var whichURL = document.URL.substr(0,document.URL.lastIndexOf('/')) + '/includes/whichbrowser/server/detect.php';

var i;
var utmfields = document.getElementsByClassName('gfield_aq_campaign');
for( i = 0; i < utmfields.length; i++) {
  if (AqCampaign != '') { document.getElementById(utmfields[i].id + '_3').value = AqCampaign.toLowerCase(); }
  if (AqSource != '') { document.getElementById(utmfields[i].id + '_1').value = AqSource.toLowerCase(); }
  if (AqMedium != '') { document.getElementById(utmfields[i].id + '_2').value = AqMedium.toLowerCase(); }
  if (AqTerm != '') { document.getElementById(utmfields[i].id + '_4').value = AqTerm.toLowerCase(); }
  if (AqContent != '') { document.getElementById(utmfields[i].id + '_5').value = AqContent.toLowerCase(); }
}

var semfields = document.getElementsByClassName('gfield_aq_sem');
for( i = 0; i < semfields.length; i++) {
  if (AqMatchType != '') { document.getElementById(semfields[i].id + '_1').value = AqMatchType.toLowerCase(); }
  if (AqGCLID != '') { document.getElementById(semfields[i].id + '_2').value = AqGCLID; }
}

var marinfields = document.getElementsByClassName('gform_aq_marin');
for( i = 0; i < marinfields.length; i++) {
	if (AqMKWID != '') { document.getElementById(semfields[i].id + '_1').value = AqMKWID; }
	if (AqPCRID != '') { document.getElementById(semfields[i].id + '_2').value = AqPCRID; }
}

function waitForWhichBrowser(cb) {
	var callback = cb;

	function wait() {
		if (typeof WhichBrowser == 'undefined')
			window.setTimeout(wait, 100)
		else
			callback();
	}

	wait();
}
document.addEventListener("DOMContentLoaded", function(event) {
	waitForWhichBrowser(function() {

		try {
			deviceinfo = new WhichBrowser();

			var deviceFields = document.getElementsByClassName('gfield_aq_deviceinfo');
			for( var i = 0; i < deviceFields.length; i++) {
				document.getElementById(deviceFields[i].id + "_1").value = deviceinfo.device.type;
				document.getElementById(deviceFields[i].id + "_2").value = deviceinfo.browser.name;
				document.getElementById(deviceFields[i].id + "_3").value = deviceinfo.os.name;
			};

		} catch(e) {
			alert(e);
		}
	});
});
