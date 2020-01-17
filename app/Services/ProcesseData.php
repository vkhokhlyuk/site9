<?php

namespace App\Services;

class ProcesseData extends Parser
{
    private const NATIONAL_NAME = 'body > div.page > div.mc-page.js-main-content > div.mc-page-content.tournament > div.page-content > div > div.teams';
    private const ALL_TEAMS_FILE_NAME_EURO_2020 = 'allTeamsHtml.html';
    private const TOUTNAMENT_NAME_XPATH = '//div[@class="tournament-header__title-name"]';
    private const EACH_TEAM_NAME = '//div[@class="teams-item__text"]';
    private const FIRST_COLUMN = 2;
    private const LAST_COLUMN = 7;
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getTournamentTitleName()
    {
        return $this->file->filterXPath(self::TOUTNAMENT_NAME_XPATH)->text();
    }

    public function getAllTeamsNames() : array
    {
        $allTeams = $this->file->filter(self::NATIONAL_NAME)->children();
        return $allTeams->each(function ($node) {
            return $node->filterXPath(self::EACH_TEAM_NAME)->children()->eq(0)->text();
        });
    }

    public function getSelectedSeasson() : array
    {
        $allSelectedSeason = $this->file->filterXPath('//div[@class="select js-tournament-header-id _disabled"]')->children();

        $existingSeasson = $allSelectedSeason->siblings()->each(function ($node) {
            $yearSeasson = $node->attr('data-year');
            $href = $node->children()->attr('data-href');
            return [$yearSeasson => $href];
        });

        if (empty($existingSeasson)) {
            $yearSeasson = $allSelectedSeason->attr('data-year');
            $href = $allSelectedSeason->children()->attr('data-href');
            $existingSeasson[] = [$yearSeasson => $href];
        }

        return $existingSeasson;
    }

    public function getAllCountriesNameWithTeamName()
    {
        $isCountry = $this->file->children()->filterXPath('//div[@class="teams-item__country"]')->text();

        if ($isCountry) {
            $countryArray = $this->file->filterXPath('//div[@class="teams-item__text"]')->each(function ($node) {
                $teamName = $node->children()->text();
                $fullName = $node->children()->last()->text();
                $filthyValueCountry = explode(', ', $fullName);
                $country = trim(array_pop($filthyValueCountry));

                return [$country => $teamName];
            });

        } else {
            $countryArray = $this->file->filterXPath('//div[@class="teams"]')->children()->each(function ($node) {
                return [$node->text() => $node->text()];
            });
        }

        return $countryArray;
    }

    public function getMainMatchesData()
    {
        $currentGroup = 0;
        $lastGroup = $this->file->filterXPath('//div[@class="table-scrollable _collapse-columns"]')
            ->evaluate('count(@td)');
        $teamsWithResultes = [];
        for ($currentGroup; $currentGroup < count($lastGroup); $currentGroup++) {
            $this->file->filterXPath('//div[@class="table-scrollable _collapse-columns"]')->eq($currentGroup)
                ->each(function ($node) use (&$teamsWithResultes) {
                    $node->children()->each(function ($nodeCh) use (&$teamsWithResultes) {
                        //all teams in one group
                        $teamsInGroup = $nodeCh->children()->children()->eq(2)->children()->each(
                            function ($nodeChild) {
                                return $nodeChild->children()->eq(1)->text();
                            }
                        );

                        //get result match and href on match
                        for ($counter = 0; $counter < count($teamsInGroup); $counter++) {
                            for ($p = self::FIRST_COLUMN; $p < self::LAST_COLUMN; $p++) {
                                $resultMatchesNotFormatted[$counter][$p][] = $nodeCh->children()->children()->eq(2)
                                    ->children() ->eq($counter)->children()->eq($p)->text();
                                try {
                                    $resultMatchesNotFormatted[$counter][$p][] = $nodeCh->children()->children()
                                        ->eq(2)->children()->eq($counter)->children()->eq($p)->children()->attr('href');

                                } catch (\Exception $exception) {
                                    //
                                }
                            }
                        }

                        //replace array keys 3, 4, => 0, 1,..
                        foreach ($resultMatchesNotFormatted as $index => $matches) {
                            foreach ($matches as $match) {
                                $formattedMatches[$index][] = $match;
                            }
                        }

                        //relations match result arrays with teams in group
                        try {
                            foreach ($teamsInGroup as $index => $team) {
                                foreach ($formattedMatches as $indexResult => $matchesCurrentTeam) {
                                    $newMatchesResult[$indexResult][$team] = $matchesCurrentTeam[$index];
                                }
                            }
                        } catch (\Exception $exception) {
                            //
                        }

                        $teamsWithResultes[] = array_combine($teamsInGroup, $newMatchesResult);
                    });
                });
        }

        return $teamsWithResultes;
    }
}