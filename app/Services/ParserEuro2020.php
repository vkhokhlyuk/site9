<?php
namespace App\Services;

use App\Models\Country;
use App\Models\Match;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentTeam;

class ParserEuro2020 extends Parser
{
    private $processor;

    public function syncPageWithFile()
    {
        $file = $this->getParseFile('allTeamsHtml.html');
        $this->crawler->addHtmlContent($file);
        $this->processor = new ProcesseData($this->crawler);
    }

    public function saveTournamentData()
    {
        $tournamentTitleName = $this->processor->getTournamentTitleName();
        $selectedSeason = $this->processor->getSelectedSeasson();
        dd($selectedSeason);

        foreach ($selectedSeason as $currentItem) {
            foreach ($currentItem as $season => $href) {
                Tournament::firstOrCreate([
                    'name'      => $tournamentTitleName,
                    'season'    => $season,
                    'link'      => $href
                ]);
            }
        }
    }

    public function syncTeamsWithTournament()
    {
        $tournament = Tournament::where('link', $this->link)->first();
        if ($tournament) {
            $allCountriesNameArray = $this->processor->getAllCountriesNameWithTeamName();

            foreach ($allCountriesNameArray as $teamData) {
                foreach ($teamData as $country => $teamName) {
                    $nationalEntity = $this->saveCountry($country);
                    $nationalId = $nationalEntity->id;

                    $entityTeam = Team::where('name', $teamName)->first();
                    if ($entityTeam) {
                        $entityTeam->country_id = $nationalId;
                        $entityTeam->save();
                    } else {
                        $entityTeam = Team::create([
                            'name'       => $teamName,
                            'country_id' => $nationalId
                        ]);
                    }

                    TournamentTeam::updateOrCreate([
                        'tournament_id' => $tournament->id,
                        'team_id'       => $entityTeam->id
                    ]);
                }
            }
            $tournament->update(['needParse' => false]);
        }
    }

    public function saveCountry(string $countryName)
    {
        $country = Country::where('name', $countryName)->first();
        if (!$country) {
            $country = Country::create([
                'name' => $countryName
            ]);
        }

        return $country;
    }

    public function saveMainMatchData()
    {
            $parseTournamentTable = $this->processor->getMainMatchesData();
            foreach ($parseTournamentTable as $groupTeamData) {
                foreach ($groupTeamData as $teamName => $matchesData) {
                    $entityTeamId = Team::where('name', $teamName)->first()->id;
                    $tournamentId = TournamentTeam::where('team_id', $entityTeamId)->first()->tournament_id;
                    foreach ($matchesData as $nameOpponent => $values) {
                        $opponentId =  Team::where('name', $nameOpponent)->first()->id;
                        if ($nameOpponent != $teamName) {
                            Match::updateOrCreate([
                                'link'          => $values[1],
                            ],[
                                'tournament_id' => $tournamentId,
                                'homeTeam_id'   => $entityTeamId,
                                'guestTeam_id'  => $opponentId,
                                'result'        => array_shift($values),
                                'link'          => array_shift($values),
                            ]);
                        }
                    }
                }
            }
    }
}