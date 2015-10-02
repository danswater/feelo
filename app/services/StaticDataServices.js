yamba.App.factory( "StaticDataServices", [ "CookieServices", "BaseServices", 
										function( CookieServices, BaseServices ) {

	return {

		
		getTimezone : function() {

			return [
	            {
	                "name": "US/Pacific",
	                "utc": "(UTC-8) Pacific Time (US & Canada)"
	            },
	            {
	                "name": "US/Mountain",
	                "utc": "(UTC-7) Mountain Time (US & Canada)"
	            },
	            {
	                "name": "US/Central",
	                "utc": "(UTC-6) Central Time (US & Canada)"
	            },
	            {
	                "name": "US/Eastern",
	                "utc": "(UTC-5) Eastern Time (US & Canada)"
	            },
	            {
	                "name": "America/Halifax",
	                "utc": "(UTC-4)  Atlantic Time (Canada)"
	            },
	            {
	                "name": "America/Anchorage",
	                "utc": "(UTC-9)  Alaska (US & Canada)"
	            },
	            {
	                "name": "Pacific/Honolulu",
	                "utc": "(UTC-10) Hawaii (US)"
	            },
	            {
	                "name": "Pacific/Samoa",
	                "utc": "(UTC-11) Midway Island, Samoa"
	            },
	            {
	                "name": "Etc/GMT-12",
	                "utc": "(UTC-12) Eniwetok, Kwajalein"
	            },
	            {
	                "name": "Canada/Newfoundland",
	                "utc": "(UTC-3:30) Canada/Newfoundland"
	            },
	            {
	                "name": "America/Buenos_Aires",
	                "utc": "(UTC-3) Brasilia, Buenos Aires, Georgetown"
	            },
	            {
	                "name": "Atlantic/South_Georgia",
	                "utc": "(UTC-2) Mid-Atlantic"
	            },
	            {
	                "name": "Atlantic/Azores",
	                "utc": "(UTC-1) Azores, Cape Verde Is."
	            },
	            {
	                "name": "Europe/London",
	                "utc": "Greenwich Mean Time (Lisbon, London)"
	            },
	            {
	                "name": "Europe/Berlin",
	                "utc": "(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid"
	            },
	            {
	                "name": "Europe/Athens",
	                "utc": "(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe"
	            },
	            {
	                "name": "Europe/Moscow",
	                "utc": "(UTC+3) Baghdad, Kuwait, Nairobi, Moscow"
	            },
	            {
	                "name": "Iran",
	                "utc": "(UTC+3:30) Tehran"
	            },
	            {
	                "name": "Asia/Dubai",
	                "utc": "(UTC+4) Abu Dhabi, Kazan, Muscat"
	            },
	            {
	                "name": "Asia/Kabul",
	                "utc": "(UTC+4:30) Kabul"
	            },
	            {
	                "name": "Asia/Yekaterinburg",
	                "utc": "(UTC+5) Islamabad, Karachi, Tashkent"
	            },
	            {
	                "name": "Asia/Dili",
	                "utc": "(UTC+5:30) Bombay, Calcutta, New Delhi"
	            },
	            {
	                "name": "Asia/Katmandu",
	                "utc": "(UTC+5:45) Nepal"
	            },
	            {
	                "name": "Asia/Omsk",
	                "utc": "(UTC+6) Almaty, Dhaka"
	            },
	            {
	                "name": "India/Cocos",
	                "utc": "(UTC+6:30) Cocos Islands, Yangon"
	            },
	            {
	                "name": "Asia/Krasnoyarsk",
	                "utc": "(UTC+7) Bangkok, Jakarta, Hanoi"
	            },
	            {
	                "name": "Asia/Hong_Kong",
	                "utc": "(UTC+8) Beijing, Hong Kong, Singapore, Taipei"
	            },
	            {
	                "name": "Asia/Tokyo",
	                "utc": "(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk"
	            },
	            {
	                "name": "Australia/Adelaide",
	                "utc": "(UTC+9:30) Adelaide, Darwin"
	            },
	            {
	                "name": "Australia/Sydney",
	                "utc": "(UTC+10) Brisbane, Melbourne, Sydney, Guam"
	            },
	            {
	                "name": "Asia/Magadan",
	                "utc": "(UTC+11) Magadan, Soloman Is., New Caledonia"
	            },
	            {
	                "name": "Pacific/Auckland",
	                "utc": "(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington"
	            }
	        ]

		}


	}

} ] );