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
      // { path: '/', expires: lifetime, secure: true, sameSite: 'Lax'  }
      Cookies.set('alq_campaign_last', JSON.stringify(this.#lastTouch), { path: '/', expires: lifetime, sameSite: 'Lax'  } );
      Cookies.set('alq_campaign', JSON.stringify(this.#firstTouch), { path: '/', expires: lifetime, sameSite: 'Lax'  } );
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
