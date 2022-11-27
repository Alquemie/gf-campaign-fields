if (typeof alqCampaignSettings == 'object') {

class AlquemieCampaignTracker {
  #firstTouch;
  #lastTouch;
  #device = {};
  #partner = {};
  #settings;
  #adKeys = {
    "gclid": "google",
    "fbclid": "facebook", 
    "gclsrc": "doubleclick",
    "mkwid": "marin", 
    "pcrid": "marin",
    "msclkid": "bing", 
    "epik": "pinterest", 
    "igshid": "instagram", 
    "gum_id": "criteo", 
    "irclickid": "impact", 
    "ttd_id": "tradedesk", 
    "clickid": "unknown", 
    "twclid": "twitter", 
    "scclid": "snapchat", 
    "ttclid": "tiktok", 
    "vmcid": "yahoo"
  };

  constructor(campaignSettings) {
    this.#firstTouch =  JSON.parse(localStorage.getItem('alq_campaign')); //Cookies.getJSON('alq_campaign');
    this.#lastTouch =  JSON.parse(localStorage.getItem('alq_campaign_last')); // Cookies.getJSON('alq_campaign_last');
    this.#settings = campaignSettings;
    this.#device = this.getDeviceInfo();

    this.getCampaignParameters();
    this.campaign2form();
    // Insert into GF Field if present
  }

  getCampaignParameters() {
    // console.log("Settings: " + JSON.stringify(this.#settings.parameters));
    let checkURL = new URL(location.href);
    let currentCampagin = {};
    for (const [key, value] of checkURL.searchParams) {
      // console.log(`${key}, ${value}`);
      let cs = this.getKeyByValue(this.#settings.parameters, key);
      if (typeof cs == 'undefined') {
        if (typeof this.#adKeys[key] != 'undefined') {
          this.#partner.name = this.#adKeys[key];
          this.#partner[key] = value;
        }
        // lookup network info
      } else {
        currentCampagin[`${cs}`] = value;
      }
    }
    if (typeof currentCampagin.campaign == 'undefined') {
      let source = '';
      let campaign = '';
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

      currentCampagin = {
        "campaign": campaign,
        "source": source.toLowerCase(),
        "medium": "",
        "term": "",
        "content": ""
      };
    }
    this.storeCampaign(currentCampagin);
  }

  getDeviceInfo() {
    return {
      os: window.navigator.platform,
      language: window.navigator.language,
      vender: window.navigator.vendor,
      userAgent: window.navigator.userAgent
    };

  }

  getKeyByValue(object, value) {
    // console.log("Lookup " + value);
    return Object.keys(object).find(key => object[key] === value);
  }
	
  storeCampaign(campaignObj) {
    // console.log("Campaign -> " + JSON.stringify(campaignObj));
    if (campaignObj.campaign != '') {
      campaignObj.device = this.#device;
      campaignObj.partner = this.#partner;

      if ( (this.#firstTouch == null) || (typeof this.#firstTouch.campaign == 'undefined')) {
        this.#firstTouch = campaignObj;
        localStorage.setItem('alq_campaign', JSON.stringify(this.#firstTouch));
        // store firstTouch in cookie
      }

      this.#lastTouch = campaignObj;
      localStorage.setItem('alq_campaign_last', JSON.stringify(this.#lastTouch));
      this.setCookies();
    }
  }

  campaign2form() {
    let theCampaign = (this.#settings.attribution == 'first') ? this.#firstTouch : this.#lastTouch;

    jQuery(("input[data-alquemie='campaign']")).val(JSON.stringify(theCampaign));
  }

  setCookies() {
    if (typeof Cookies != 'undefined') {
      let lifetime = (typeof(this.#settings.cookieLife) === "number") ? this.#settings.cookieLife : 30;
      Cookies.set('alq_campaign_last', this.#lastTouch, { path: '/', expires: lifetime, secure: true, sameSite: 'Lax'  } );
      Cookies.set('alq_campaign', this.#firstTouch, { path: '/', expires: lifetime, secure: true, sameSite: 'Lax'  } );
    }
  }

  getCookies() {
    // Fall back to cookies if values are not in local storage
    if (typeof Cookies != 'undefined') {
      if (this.#firstTouch == null) {
        this.#firstTouch = Cookies.getJSON('alq_campaign');
      }
      if (this.#lastTouch == null) {
        this.#lastTouch = Cookies.getJSON('alq_campaign_last');
      }
    }
  }
};

let AlquemieCampaigns = new AlquemieCampaignTracker(alqCampaignSettings); 
}
/*

  lastTouch: Cookies.getJSON('alq_campaign_last')

  firstTouch: Cookies.getJSON('alq_campaign')

getUrlParameter: function (name) {
		name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
		var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
		var results = regex.exec(location.search);
		return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
	}


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
*/