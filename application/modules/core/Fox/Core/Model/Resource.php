<?php

/**
 * Zendfox Framework
 *
 * LICENSE
 *
 * This file is part of Zendfox.
 *
 * Zendfox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zendfox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zendfox in the file LICENSE.txt.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Class Fox_Core_Model_Cache
 * 
 * @uses Uni_Core_Model
 * @category    Fox
 * @package     Fox_Core
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in>
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Fox_Core_Model_Resource {
    
    /**
     * Allowed locales
     * 
     * @var array
     */
    protected static $_allowedLocales = array(
        'af_ZA' => 'Afrikaans (South Africa)', 'ar_DZ' => 'Arabic (Algeria)', 'ar_EG' => 'Arabic (Egypt)',
        'ar_KW' => 'Arabic (Kuwait)', 'ar_MA' => 'Arabic (Morocco)', 'ar_SA' => 'Arabic (Saudi Arabia)',
        'az_AZ' => 'Azerbaijani (Azerbaijan)', 'be_BY' => 'Belarusian (Belarus)', 'bg_BG' => 'Bulgarian (Bulgaria)',
        'bn_BD' => 'Bengali (Bangladesh)', 'bs_BA' => 'Bosnian (Bosnia)', 'ca_ES' => 'Catalan (Catalonia)',
        'cs_CZ' => 'Czech (Czech Republic)', 'cy_GB' => 'Welsh (United Kingdom)', 'da_DK' => 'Danish (Denmark)',
        'de_AT' => 'German (Austria)', 'de_CH' => 'German (Switzerland)', 'de_DE' => 'German (Germany)',
        'el_GR' => 'Greek (Greece)', 'en_AU' => 'English (Australian)', 'en_CA' => 'English (Canadian)',
        'en_GB' => 'English (United Kingdom)', 'en_NZ' => 'English (New Zealand)', 'en_US' => 'English (United States)',
        'es_AR' => 'Spanish (Argentina)', 'es_CO' => 'Spanish (Colombia)', 'es_PA' => 'Spanish (Panama)',
        'gl_ES' => 'Galician (Galician)', 'es_CR' => 'Spanish (Costa Rica)', 'es_ES' => 'Spanish (Spain)',
        'es_MX' => 'Spanish (Mexico)', 'es_EU' => 'Basque (Basque)', 'es_PE' => 'Spanish (Peru)',
        'et_EE' => 'Estonian (Estonia)', 'fa_IR' => 'Persian (Iran)', 'fi_FI' => 'Finnish (Finland)',
        'fil_PH' => 'Filipino (Philippines)', 'fr_CA' => 'French (Canada)', 'fr_FR' => 'French (France)',
        'gu_IN' => 'Gujarati (India)', 'he_IL' => 'Hebrew (Israel)', 'hi_IN' => 'Hindi (India)',
        'hr_HR' => 'Croatian (Croatia)', 'hu_HU' => 'Hungarian (Hungary)', 'id_ID' => 'Indonesian (Indonesia)',
        'is_IS' => 'Icelandic (Iceland)', 'it_CH' => 'Italian (Switzerland)', 'it_IT' => 'Italian (Italy)',
        'ja_JP' => 'Japanese (Japan)', 'ka_GE' => 'Georgian (Georgia)', 'km_KH' => 'Khmer (Cambodia)',
        'ko_KR' => 'Korean (South Korea)', 'lo_LA' => 'Lao (Laos)', 'lt_LT' => 'Lithuanian (Lithuania)',
        'lv_LV' => 'Latvian (Latvia)', 'mk_MK' => 'Macedonian (Macedonia)', 'mn_MN' => 'Mongolian (Mongolia)',
        'ms_MY' => 'Malaysian (Malaysia)', 'nl_NL' => 'Dutch (Netherlands)', 'nb_NO' => 'Norwegian BokmГ_l (Norway)',
        'nn_NO' => 'Norwegian Nynorsk (Norway)', 'pl_PL' => 'Polish (Poland)', 'pt_BR' => 'Portuguese (Brazil)',
        'pt_PT' => 'Portuguese (Portugal)', 'ro_RO' => 'Romanian (Romania)', 'ru_RU' => 'Russian (Russia)',
        'sk_SK' => 'Slovak (Slovakia)', 'sl_SI' => 'Slovenian (Slovenia)', 'sq_AL' => 'Albanian (Albania)',
        'sr_RS' => 'Serbian (Serbia)', 'sv_SE' => 'Swedish (Sweden)', 'sw_KE' => 'Swahili (Kenya)',
        'th_TH' => 'Thai (Thailand)', 'tr_TR' => 'Turkish (Turkey)', 'uk_UA' => 'Ukrainian (Ukraine)',
        'vi_VN' => 'Vietnamese (Vietnam)', 'zh_CN' => 'Chinese (China)', 'zh_HK' => 'Chinese (Hong Kong SAR)',
        'zh_TW' => 'Chinese (Taiwan)', 'es_CL' => 'Spanich (Chile)', 'lo_LA' => 'Laotian',
        'es_VE' => 'Spanish (Venezuela)'
    );
    
    /**
     * Default locale
     * 
     * @var string
     */
    protected static $_defaultLocale='en_US';
    
    /**
     * Allowed timezones
     * 
     * @var array
     */
    protected static $_allowedTimezones = array(
        'Australia/Darwin' => 'AUS Central Standard Time (Australia/Darwin)',
        'Australia/Sydney' => 'AUS Eastern Standard Time (Australia/Sydney)',
        'Asia/Kabul' => 'Afghanistan Standard Time (Asia/Kabul)',
        'America/Anchorage' => 'Alaskan Standard Time (America/Anchorage)',
        'Asia/Riyadh' => 'Arab Standard Time (Asia/Riyadh)',
        'Asia/Dubai' => 'Arabian Standard Time (Asia/Dubai)',
        'Asia/Baghdad' => 'Arabic Standard Time (Asia/Baghdad)',
        'America/Buenos_Aires' => 'Argentina Standard Time (America/Buenos_Aires)',
        'Asia/Yerevan' => 'Armenian Standard Time (Asia/Yerevan)',
        'America/Halifax' => 'Atlantic Standard Time (America/Halifax)',
        'Asia/Baku' => 'Azerbaijan Standard Time (Asia/Baku)',
        'Atlantic/Azores' => 'Azores Standard Time (Atlantic/Azores)',
        'America/Regina' => 'Canada Central Standard Time (America/Regina)',
        'Atlantic/Cape_Verde' => 'Cape Verde Standard Time (Atlantic/Cape_Verde)',
        'Asia/Tbilisi' => 'Caucasus Standard Time (Asia/Tbilisi)',
        'Australia/Adelaide' => 'Cen. Australia Standard Time (Australia/Adelaide)',
        'America/Guatemala' => 'Central America Standard Time (America/Guatemala)',
        'Asia/Dhaka' => 'Central Asia Standard Time (Asia/Dhaka)',
        'America/Manaus' => 'Central Brazilian Standard Time (America/Manaus)',
        'Europe/Budapest' => 'Central Europe Standard Time (Europe/Budapest)',
        'Europe/Warsaw' => 'Central European Standard Time (Europe/Warsaw)',
        'Pacific/Guadalcanal' => 'Central Pacific Standard Time (Pacific/Guadalcanal)',
        'America/Chicago' => 'Central Standard Time (America/Chicago)',
        'America/Mexico_City' => 'Central Standard Time (Mexico) (America/Mexico_City)',
        'Asia/Shanghai' => 'China Standard Time (Asia/Shanghai)',
        'Etc/GMT+12' => 'Dateline Standard Time (Etc/GMT+12)',
        'Africa/Nairobi' => 'E. Africa Standard Time (Africa/Nairobi)',
        'Australia/Brisbane' => 'E. Australia Standard Time (Australia/Brisbane)',
        'Europe/Minsk' => 'E. Europe Standard Time (Europe/Minsk)',
        'America/Sao_Paulo' => 'E. South America Standard Time (America/Sao_Paulo)',
        'America/New_York' => 'Eastern Standard Time (America/New_York)',
        'Africa/Cairo' => 'Egypt Standard Time (Africa/Cairo)',
        'Asia/Yekaterinburg' => 'Ekaterinburg Standard Time (Asia/Yekaterinburg)',
        'Europe/Kiev' => 'FLE Standard Time (Europe/Kiev)',
        'Pacific/Fiji' => 'Fiji Standard Time (Pacific/Fiji)',
        'Europe/London' => 'GMT Standard Time (Europe/London)',
        'Europe/Istanbul' => 'GTB Standard Time (Europe/Istanbul)',
        'Etc/GMT-3' => 'Georgian Standard Time (Etc/GMT-3)',
        'America/Godthab' => 'Greenland Standard Time (America/Godthab)',
        'Africa/Casablanca' => 'Greenwich Standard Time (Africa/Casablanca)',
        'Pacific/Honolulu' => 'Hawaiian Standard Time (Pacific/Honolulu)',
        'Asia/Kolkata' => 'India Standard Time (Asia/Kolkata)',
        'Asia/Tehran' => 'Iran Standard Time (Asia/Tehran)',
        'Asia/Jerusalem' => 'Israel Standard Time (Asia/Jerusalem)',
        'Asia/Amman' => 'Jordan Standard Time (Asia/Amman)',
        'Asia/Seoul' => 'Korea Standard Time (Asia/Seoul)',
        'America/Chihuahua' => 'Mexico Standard Time 2 (America/Chihuahua)',
        'Atlantic/South_Georgia' => 'Mid-Atlantic Standard Time (Atlantic/South_Georgia)',
        'Asia/Beirut' => 'Middle East Standard Time (Asia/Beirut)',
        'America/Montevideo' => 'Montevideo Standard Time (America/Montevideo)',
        'America/Denver' => 'Mountain Standard Time (America/Denver)',
        'Asia/Rangoon' => 'Myanmar Standard Time (Asia/Rangoon)',
        'Asia/Novosibirsk' => 'N. Central Asia Standard Time (Asia/Novosibirsk)',
        'Africa/Windhoek' => 'Namibia Standard Time (Africa/Windhoek)',
        'Asia/Katmandu' => 'Nepal Standard Time (Asia/Katmandu)',
        'Pacific/Auckland' => 'New Zealand Standard Time (Pacific/Auckland)',
        'America/St_Johns' => 'Newfoundland Standard Time (America/St_Johns)',
        'Asia/Irkutsk' => 'North Asia East Standard Time (Asia/Irkutsk)',
        'Asia/Krasnoyarsk' => 'North Asia Standard Time (Asia/Krasnoyarsk)',
        'America/Santiago' => 'Pacific SA Standard Time (America/Santiago)',
        'America/Los_Angeles' => 'Pacific Standard Time (America/Los_Angeles)',
        'America/Tijuana' => 'Pacific Standard Time (Mexico) (America/Tijuana)',
        'Europe/Paris' => 'Romance Standard Time (Europe/Paris)',
        'Europe/Moscow' => 'Russian Standard Time (Europe/Moscow)',
        'Etc/GMT+3' => 'SA Eastern Standard Time (Etc/GMT+3)',
        'America/Bogota' => 'SA Pacific Standard Time (America/Bogota)', 'America/La_Paz' => 'SA Western Standard Time (America/La_Paz)', 'Asia/Bangkok' => 'SE Asia Standard Time (Asia/Bangkok)', 'Pacific/Apia' => 'Samoa Standard Time (Pacific/Apia)', 'Asia/Singapore' => 'Singapore Standard Time (Asia/Singapore)', 'Africa/Johannesburg' => 'South Africa Standard Time (Africa/Johannesburg)', 'Asia/Colombo' => 'Sri Lanka Standard Time (Asia/Colombo)', 'Asia/Taipei' => 'Taipei Standard Time (Asia/Taipei)', 'Australia/Hobart' => 'Tasmania Standard Time (Australia/Hobart)', 'Asia/Tokyo' => 'Tokyo Standard Time (Asia/Tokyo)', 'Pacific/Tongatapu' => 'Tonga Standard Time (Pacific/Tongatapu)', 'Etc/GMT+5' => 'US Eastern Standard Time (Etc/GMT+5)', 'America/Phoenix' => 'US Mountain Standard Time (America/Phoenix)', 'America/Caracas' => 'Venezuela Standard Time (America/Caracas)', 'Asia/Vladivostok' => 'Vladivostok Standard Time (Asia/Vladivostok)', 'Australia/Perth' => 'W. Australia Standard Time (Australia/Perth)', 'Africa/Lagos' => 'W. Central Africa Standard Time (Africa/Lagos)', 'Europe/Berlin' => 'W. Europe Standard Time (Europe/Berlin)', 'Asia/Karachi' => 'West Asia Standard Time (Asia/Karachi)', 'Pacific/Port_Moresby' => 'West Pacific Standard Time (Pacific/Port_Moresby)', 'Asia/Yakutsk' => 'Yakutsk Standard Time (Asia/Yakutsk)',
    );
    
    /**
     * Default timezone
     * 
     * @var string
     */
    protected static $_defaultTimezone='America/Chicago';
    
    /**
     * Get all the allowed locales
     * 
     * @return array
     */
    public static function getAllowedLocales(){
        return self::$_allowedLocales;
    }
    
    /**
     * Get default locale
     * 
     * @return string
     */
    public static function getDefaultLocale(){
        return self::$_defaultLocale;
    }
    
    /**
     * Get all the allowed timezones
     * 
     * @return array
     */
    public static function getAllowedTimezones(){
        return self::$_allowedTimezones;
    }
    
    /**
     * Get default timezone
     * 
     * @return string
     */
    public static function getDefaultTimezone(){
        return self::$_defaultTimezone;
    }
    
}