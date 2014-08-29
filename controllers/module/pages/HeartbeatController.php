<?php namespace Cysha\Modules\Darchoods\Controllers\Module\Pages;

use Cysha\Modules\Darchoods\Controllers\Module\BaseController as BMC;
use Cysha\Modules\Darchoods\Helpers\IRC as IRC;
use Cysha\Modules\Darchoods\Repositories\Irc\User\RepositoryInterface as IrcUserRepository;
use Cysha\Modules\Darchoods\Repositories\Irc\Server\RepositoryInterface as IrcServerRepository;
use Illuminate\Support\Collection;
use Auth;
use DB;
use URL;
use Session;
use Config;
use Str;
use Cache;

class HeartbeatController extends BMC
{
    public function __construct(IrcUserRepository $user, IrcServerRepository $server)
    {
        parent::__construct();
        $this->ircUser = $user;
        $this->ircServer = $server;
    }

    public function getIndex()
    {
        $this->setTitle('Network Heartbeat <div class="animated pulse"><i class="fa fa-heart"></i></div>');
        $this->setDecorativeMode(); // makes the page get a funky wrapper on DH only

        // load in the assets for the graph
        $this->objTheme->asset()->add('d3', 'packages/module/darchoods/assets/d3.js');
        $this->objTheme->asset()->add('c3-js', 'packages/module/darchoods/assets/chart/js/c3.min.js', ['d3']);
        $this->objTheme->asset()->add('c3-css', 'packages/module/darchoods/assets/chart/css/c3.css', ['d3']);
        $this->objTheme->asset()->add('wm-js', 'packages/module/darchoods/assets/chart/js/worldmap.js', ['d3']);
        $this->objTheme->asset()->add('wm-topo-js', 'packages/module/darchoods/assets/chart/js/topojson.v1.min.js', ['wm-js']);


        return $this->setView('pages.heartbeat.index', [
            'serverList'  => $this->getCollection(),
            'userStats'   => $this->getUserStats(),
            'clientStats' => $this->getClientStats(),
        ]);
    }

    public function getCollection()
    {
        try {
            $dbServers = $this->ircServer->getAll();
        } catch (\PDOException $e) {
            Session::flash('error', 'Cannot get server list from IRC.');
            return [];
        }

        if (!count($dbServers)) {
            return [];
        }
// echo \Debug::dump($dbServers, '');die;
        $nicks = [];
        if (!Auth::guest()) {
            $nicks = DB::connection('denora')->table('user')->whereAccount(Auth::user()->username)->select('nick', 'server')->get();
        }

        $dbServers = array_filter($dbServers, function (&$server) use ($nicks) {
            if (count($nicks)) {
                foreach ($nicks as $nick) {
                    if ($server['name'] == $nick->server) {
                        $server['location'] = (array) $nick;
                    }
                }
            }

            return true;
        });

        usort($dbServers, function ($x, $y) {
            if ($x['country']['code'] == $y['country']['code']) {
                return 0;
            } elseif ($x['country']['code'] < $y['country']['code']) {
                return 1;
            } else {
                return -1;
            }
        });

        return $dbServers;
    }

    /**
     * Gets the user count from Denora and returns them
     *
     * @return array
     */
    public function getUserStats()
    {

        $graph = $this->ircServer->getUserCount(24);

        $js = 'jQuery(window).ready(function () {
            var userChart = c3.generate({
                bindto: \'#users\',
                height: 300,
                width: 300,
                data: {
                    x: \'x\',
                    columns: [
                        [\'x\', '.implode(', ', array_keys($graph)).'],
                        [\'Users\', '.implode(', ', array_values($graph)).']
                    ]
                },
                axis: {
                    x: {
                        type: \'timeseries\',
                        tick: {
                            format: \'%H:%M:%S\'
                        }
                    }
                }
            });
        });';


        $this->objTheme->asset()->writeScript('userStats', $js, ['c3-js']);

        return $graph;
    }

    /**
     * Gets the client counts from Denora and returns them
     *
     * @return array
     */
    public function getClientStats()
    {

        $clients = $this->ircUser->getClientVersions();
        if (!count($clients)) {
            return [];
        }

        $output = [];
        foreach ($clients as list($ident, $count)) {
            $output[] = '["'.$ident.'", '.$count.']';
        }

        $ident = null;
        if (!Auth::guest()) {
            $nick = DB::connection('denora')->table('user')->whereAccount(Auth::user()->username)->select('nick', 'ctcpversion')->get();

            if (count($nick) > 0) {
                $nick = head($nick);
                $ident = Str::lower(str_replace('"', '', head(explode(' ', $nick->ctcpversion))));
            }
        }

        $js = 'jQuery(window).ready(function () {
            var clientChart = c3.generate({
                bindto: \'#clients\',
                height: 300,
                width: 300,
                data: {
                    columns: ['.implode(', ', $output).'],
                    type: \'donut\'
                },
                donut: {
                    title: "'.$ident.'".length > 0 ? "You are using '.$ident.'" : "Clients Used",
                    label: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            });
        });';


        $this->objTheme->asset()->writeScript('clientStats', $js, ['c3-js']);

        return [];
    }

    /**
     * Gets the users country statistics from the db and outputs them in a csv format
     */
    public function getCountryStats()
    {
        // SELECT count(nickid) as count, countrycode, country FROM user GROUP BY countrycode ORDER BY count
        $countries = DB::connection('denora')->table('user')
            ->select(DB::raw('count(nickid) as `count`, `countrycode`, `country`'))
            ->groupBy('countrycode')
            ->orderBy('count', 'desc')
            ->get();

        $country_codes = ['afghanistan' => 'AF', 'angola' => 'AO', 'albania' => 'AL', 'united arab emirates' => 'AE', 'argentina' => 'AR', 'armenia' => 'AM', 'antarctica' => 'AY', 'french southern and antarctic lands' => 'FS', 'australia' => 'AS', 'austria' => 'AU', 'azerbaijan' => 'AJ', 'burundi' => 'BY', 'belgium' => 'BE', 'benin' => 'BN', 'burkina faso' => 'UV', 'bangladesh' => 'BG', 'bulgaria' => 'BU', 'bahamas' => 'BF', 'bosnia and herzegovina' => 'BK', 'belarus' => 'BO', 'belize' => 'BH', 'bolivia' => 'BL', 'brazil' => 'BR', 'brunei' => 'BX', 'bhutan' => 'BT', 'botswana' => 'BC', 'central african republic' => 'CT', 'canada' => 'CA', 'switzerland' => 'SZ', 'chile' => 'CI', 'china' => 'CH', 'cote d\'ivoire' => 'IV', 'cameroon' => 'CM', 'democratic republic of the congo' => 'CG', 'republic of the congo' => 'CF', 'colombia' => 'CO', 'costa rica' => 'CS', 'cuba' => 'CU', 'northern cyprus' => '991', 'cyprus' => 'CY', 'czech republic' => 'EZ', 'germany' => 'GM', 'djibouti' => 'DJ', 'denmark' => 'DA', 'dominican republic' => 'DR', 'algeria' => 'AG', 'ecuador' => 'EC', 'egypt' => 'EG', 'eritrea' => 'ER', 'spain' => 'SP', 'estonia' => 'EN', 'ethiopia' => 'ET', 'finland' => 'FI', 'fiji' => 'FJ', 'falkland islands' => 'FK', 'france' => 'FR', 'gabon' => 'GB', 'united kingdom' => 'UK', 'georgia' => 'GG', 'ghana' => 'GH', 'guinea' => 'GV', 'the gambia' => 'GA', 'guinea-bissau' => 'PU', 'equatorial guinea' => 'EK', 'greece' => 'GR', 'greenland' => 'GL', 'guatemala' => 'GT', 'guyana' => 'GY', 'honduras' => 'HO', 'croatia' => 'HR', 'haiti' => 'HA', 'hungary' => 'HU', 'indonesia' => 'ID', 'india' => 'IN', 'ireland' => 'EI', 'iran' => 'IR', 'iraq' => 'IZ', 'iceland' => 'IC', 'israel' => 'IS', 'italy' => 'IT', 'jamaica' => 'JM', 'jordan' => 'JO', 'japan' => 'JA', 'kazakhstan' => 'KZ', 'kenya' => 'KE', 'kyrgyzstan' => 'KG', 'cambodia' => 'CB', 'south korea' => 'KS', 'kosovo' => 'KV', 'kuwait' => 'KU', 'laos' => 'LA', 'lebanon' => 'LE', 'liberia' => 'LI', 'libya' => 'LY', 'sri lanka' => 'CE', 'lesotho' => 'LT', 'lithuania' => 'LH', 'luxembourg' => 'LU', 'latvia' => 'LG', 'morocco' => 'MO', 'moldova' => 'MD', 'madagascar' => 'MA', 'mexico' => 'MX', 'macedonia' => 'MK', 'mali' => 'ML', 'burma' => 'BM', 'montenegro' => 'MJ', 'mongolia' => 'MG', 'mozambique' => 'MZ', 'mauritania' => 'MR', 'malawi' => 'MI', 'malaysia' => 'MY', 'namibia' => 'WA', 'new caledonia' => 'NC', 'niger' => 'NG', 'nigeria' => 'NI', 'nicaragua' => 'NU', 'netherlands' => 'NL', 'norway' => 'NO', 'nepal' => 'NP', 'new zealand' => 'NZ', 'oman' => 'MU', 'pakistan' => 'PK', 'panama' => 'PM', 'peru' => 'PE', 'philippines' => 'RP', 'papua new guinea' => 'PP', 'poland' => 'PL', 'puerto rico' => 'RQ', 'north korea' => 'KN', 'portugal' => 'PO', 'paraguay' => 'PA', 'palestine' => '275', 'qatar' => 'QA', 'romania' => 'RO', 'russian federation' => 'RS', 'rwanda' => 'RW', 'western sahara' => 'WI', 'saudi arabia' => 'SA', 'sudan' => 'SU', 'south sudan' => 'OD', 'senegal' => 'SG', 'solomon islands' => 'BP', 'sierra leone' => 'SL', 'el salvador' => 'ES', 'somaliland' => '993', 'somalia' => 'SO', 'serbia' => 'RI', 'suriname' => 'NS', 'slovakia' => 'LO', 'slovenia' => 'SI', 'sweden' => 'SW', 'swaziland' => 'WZ', 'syria' => 'SY', 'chad' => 'CD', 'togo' => 'TO', 'thailand' => 'TH', 'tajikistan' => 'TI', 'turkmenistan' => 'TX', 'timor-leste' => 'TT', 'trinidad and tobago' => 'TD', 'tunisia' => 'TS', 'turkey' => 'TU', 'taiwan' => 'TW', 'tanzania' => 'TZ', 'uganda' => 'UG', 'ukraine' => 'UP', 'uruguay' => 'UY', 'united states' => 'US', 'uzbekistan' => 'UZ', 'venezuela' => 'VE', 'vietnam' => 'VM', 'vanuatu' => 'NH', 'yemen' => 'YM', 'south africa' => 'SF', 'zambia' => 'ZA', 'zimbabwe' => 'ZI', ];

        // output CSV for the world map
        echo 'country,population',"\r\n";
        foreach ($countries as $country) {
            if ($country->countrycode == '??') {
                continue;
            }

            if (!isset($country_codes[Str::lower($country->country)])) {
                continue;
            }
            echo \Str::lower($country_codes[Str::lower($country->country)]),',',$country->count,"\r\n";
        }
        exit;
    }
}
