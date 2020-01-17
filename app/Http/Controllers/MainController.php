<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Services\ParserEuro2020;

class MainController
{
    private const LINK_FOR_PAGES = [
//        '/football/_euro/tournament/2847/',
//        '/football/_ucl/tournament/2989/',
//        '/football/_europeleague/tournament/2991/',
        '/football/_worldcup/tournament/4/'
    ];

    private const ADDITIONAL_URL_TABLE_EURO = 'table/#group';

    public function main()
    {
        return view('main');
    }

    public function updateMatches()
    {
        foreach (self::LINK_FOR_PAGES as $simpleLink) {
            $parser = new ParserEuro2020($simpleLink);
            $parser->syncPageWithFile();
            $parser->saveTournamentData();
            $parser->saveMainMatchData();
        }

        return view('main');
    }

    public function getMatches()
    {
        $tournamentLinks = Tournament::where('needParse', true)->where('link', '!=', null)->pluck('link');
        foreach ($tournamentLinks as $link) {
            $parser = new ParserEuro2020($link);
            $parser->syncPageWithFile();
            $parser->saveTournamentData();
            $parser->syncTeamsWithTournament();
        }

        foreach ($tournamentLinks as $link) {
            $parserTable = new ParserEuro2020($link, self::ADDITIONAL_URL_TABLE_EURO);
            $parserTable->syncPageWithFile();
            $parserTable->saveMainMatchData();
        }

        return compact('main', 'list');
    }
}