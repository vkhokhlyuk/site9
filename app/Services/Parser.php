<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    private const DEFAULT_URL = 'https://www.championat.com';

    public $crawler;
    public $folderName;
    public $link;

    public function __construct(string $link, string $entity = 'teams')
    {
        $this->link = $link;
        $this->createParseFile(self::DEFAULT_URL . $link . $entity);
        $this->crawler = new Crawler();
    }

    public function getParseFile(string $fileNameWithExtension) : string
    {
        return file_get_contents(public_path() . '\\' . $this->folderName . '\\' . $fileNameWithExtension);
    }

    private function createParseFile(string $link) : void
    {
        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //CHANGE THIS
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
        $html = curl_exec($ch);

        $this->getFolderName($link);
        $this->hasFolder();

        file_put_contents(public_path() . '/' . $this->folderName . '/' . 'allTeamsHtml.html', $html);
    }

    private function getFolderName($link) : void
    {
        $subStrFromSpecificSymbol = stristr($link, '_'); // _euro/bla/bla
        $this->folderName = stristr($subStrFromSpecificSymbol, '/', true); // _euro
    }

    private function hasFolder()
    {
        if (!file_exists(public_path() . '/' . $this->folderName)) {
            mkdir(public_path() . '/' . $this->folderName);
        }
    }
}