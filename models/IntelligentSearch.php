<?php

class IntelligentSearch extends SearchType {
    
    private static $STOPWORDS = array(
			"ab",  "bei",  "da",  "deshalb",  "ein",  "f�r",  "finde",  "haben",  "hier",  "ich",  "ja", 
			"kann",  "machen",  "muesste",  "nach",  "oder",  "seid",  "sonst",  "und",  "vom",  "wann",  "wenn", 
			"wie",  "zu",  "bin",  "eines",  "hat",  "manche",  "solches",  "an",  "anderm",  "bis",  "das",  "deinem", 
			"demselben",  "dir",  "doch",  "einig",  "er",  "eurer",  "hatte",  "ihnen",  "ihre",  "ins",  "jenen", 
			"keinen",  "manchem",  "meinen",  "nichts",  "seine",  "soll",  "unserm",  "welche",  "werden",  "wollte", 
			"w�hrend",  "alle",  "allem",  "allen",  "aller",  "alles",  "als",  "also",  "am",  "ander",  "andere", 
			"anderem",  "anderen",  "anderer",  "anderes",  "andern",  "anders",  "auch",  "auf",  "aus",  "bist", 
			"bsp.",  "daher",  "damit",  "dann",  "dasselbe",  "dazu",  "da�",  "dein",  "deine",  "deinen", 
			"deiner",  "deines",  "dem",  "den",  "denn",  "denselben",  "der",  "derer",  "derselbe", 
			"derselben",  "des",  "desselben",  "dessen",  "dich",  "die",  "dies",  "diese",  "dieselbe", 
			"dieselben",  "diesem",  "diesen",  "dieser",  "dieses",  "dort",  "du",  "durch",  "eine",  "einem", 
			"einen",  "einer",  "einige",  "einigem",  "einigen",  "einiger",  "einiges",  "einmal",  "es",  "etwas", 
			"euch",  "euer",  "eure",  "eurem",  "euren",  "eures",  "ganz",  "ganze",  "ganzen",  "ganzer", 
			"ganzes",  "gegen",  "gemacht",  "gesagt",  "gesehen",  "gewesen",  "gewollt",  "hab",  "habe", 
			"hatten",  "hin",  "hinter",  "ihm",  "ihn",  "ihr",  "ihrem",  "ihren",  "ihrer",  "ihres", 
			"im",  "in",  "indem",  "ist",  "jede",  "jedem",  "jeden",  "jeder",  "jedes",  "jene",  "jenem", 
			"jener",  "jenes",  "jetzt",  "kein",  "keine",  "keinem",  "keiner",  "keines",  "konnte",  "k�nnten", 
			"k�nnen",  "k�nnte",  "mache",  "machst",  "macht",  "machte",  "machten",  "man",  "manchen",  "mancher", 
			"manches",  "mein",  "meine",  "meinem",  "meiner",  "meines",  "mich",  "mir",  "mit",  "muss", 
			"musste",  "m��t",  "nicht",  "noch",  "nun",  "nur",  "ob",  "ohne",  "sage",  "sagen",  "sagt", 
			"sagte",  "sagten",  "sagtest",  "sehe",  "sehen",  "sehr",  "seht",  "sein",  "seinem",  "seinen", 
			"seiner",  "seines",  "selbst",  "sich",  "sicher",  "sie",  "sind",  "so",  "solche",  "solchem", 
			"solchen",  "solcher",  "sollte",  "sondern",  "um",  "uns",  "unse",  "unsen",  "unser",  "unses", 
			"unter",  "viel",  "von",  "vor",  "war",  "waren",  "warst",  "was",  "weg",  "weil",  "weiter", 
			"welchem",  "welchen",  "welcher",  "welches",  "welche",  "werde",  "wieder",  "will",  "wir",  "wird", 
			"wirst",  "wo",  "wolle",  "wollen",  "wollt",  "wollten",  "wolltest",  "wolltet",  "w�rde",  "w�rden", 
			"z.B.",  "zum",  "zur",  "zwar",  "zwischen",  "�ber",  "aber",  "abgerufen",  "abgerufene", 
			"abgerufener",  "abgerufenes",  "acht",  "allein",  "allerdings",  "allerlei",  "allgemein", 
			"allm�hlich",  "allzu",  "alsbald",  "andererseits",  "andernfalls",  "anerkannt",  "anerkannte", 
			"anerkannter",  "anerkanntes",  "anfangen",  "anfing",  "angefangen",  "angesetze",  "angesetzt", 
			"angesetzten",  "angesetzter",  "ansetzen",  "anstatt",  "arbeiten",  "aufgeh�rt",  "aufgrund", 
			"aufh�ren",  "aufh�rte",  "aufzusuchen",  "ausdr�cken",  "ausdr�ckt",  "ausdr�ckte",  "ausgenommen", 
			"ausser",  "ausserdem",  "author",  "autor",  "au�en",  "au�er",  "au�erdem",  "au�erhalb",  "bald", 
			"bearbeite",  "bearbeiten",  "bearbeitete",  "bearbeiteten",  "bedarf",  "bedurfte",  "bed�rfen", 
			"befragen",  "befragte",  "befragten",  "befragter",  "begann",  "beginnen",  "begonnen",  "behalten", 
			"behielt",  "beide",  "beiden",  "beiderlei",  "beides",  "beim",  "bei",  "beinahe",  "beitragen", 
			"beitrugen",  "bekannt",  "bekannte",  "bekannter",  "bekennen",  "benutzt",  "bereits",  "berichten", 
			"berichtet",  "berichtete",  "berichteten",  "besonders",  "besser",  "bestehen",  "besteht", 
			"betr�chtlich",  "bevor",  "bez�glich",  "bietet",  "bisher",  "bislang",  "bis",  "bleiben", 
			"blieb",  "bloss",  "blo�",  "brachte",  "brachten",  "brauchen",  "braucht",  "bringen",  "br�uchte", 
			"bzw",  "b�den",  "ca.",  "dabei",  "dadurch",  "daf�r",  "dagegen",  "dahin",  "damals",  "danach", 
			"daneben",  "dank",  "danke",  "danken",  "dannen",  "daran",  "darauf",  "daraus",  "darf",  "darfst", 
			"darin",  "darum",  "darunter",  "dar�ber",  "dar�berhinaus",  "dass",  "davon",  "davor",  "demnach", 
			"denen",  "dennoch",  "derart",  "derartig",  "derem",  "deren",  "derjenige",  "derjenigen",  "derzeit", 
			"desto",  "deswegen",  "diejenige",  "diesseits",  "dinge",  "direkt",  "direkte",  "direkten", 
			"direkter",  "doppelt",  "dorther",  "dorthin",  "drauf",  "drei",  "drei�ig",  "drin",  "dritte", 
			"drunter",  "dr�ber",  "dunklen",  "durchaus",  "durfte",  "durften",  "d�rfen",  "d�rfte",  "eben", 
			"ebenfalls",  "ebenso",  "ehe",  "eher",  "eigenen",  "eigenes",  "eigentlich",  "einba�n", 
			"einerseits",  "einfach",  "einf�hren",  "einf�hrte",  "einf�hrten",  "eingesetzt",  "einigerma�en", 
			"eins",  "einseitig",  "einseitige",  "einseitigen",  "einseitiger",  "einst",  "einstmals",  "einzig", 
			"ende",  "entsprechend",  "entweder",  "erg�nze",  "erg�nzen",  "erg�nzte",  "erg�nzten",  "erhalten", 
			"erhielt",  "erhielten",  "erh�lt",  "erneut",  "erst",  "erste",  "ersten",  "erster",  "er�ffne", 
			"er�ffnen",  "er�ffnet",  "er�ffnete",  "er�ffnetes",  "etc",  "etliche",  "etwa",  "fall",  "falls", 
			"fand",  "fast",  "ferner",  "finden",  "findest",  "findet",  "folgende",  "folgenden",  "folgender", 
			"folgendes",  "folglich",  "fordern",  "fordert",  "forderte",  "forderten",  "fortsetzen",  "fortsetzt", 
			"fortsetzte",  "fortsetzten",  "fragte",  "frau",  "frei",  "freie",  "freier",  "freies",  "fuer", 
			"f�nf",  "gab",  "ganzem",  "gar",  "gbr",  "geb",  "geben",  "geblieben",  "gebracht",  "gedurft", 
			"geehrt",  "geehrte",  "geehrten",  "geehrter",  "gefallen",  "gefiel",  "gef�lligst",  "gef�llt", 
			"gegeben",  "gehabt",  "gehen",  "geht",  "gekommen",  "gekonnt",  "gemocht",  "gem�ss",  "genommen", 
			"genug",  "gern",  "gestern",  "gestrige",  "getan",  "geteilt",  "geteilte",  "getragen", 
			"gewisserma�en",  "geworden",  "ggf",  "gib",  "gibt",  "gleich",  "gleichwohl",  "gleichzeitig", 
			"gl�cklicherweise",  "gmbh",  "gratulieren",  "gratuliert",  "gratulierte",  "gut",  "gute",  "guten", 
			"g�ngig",  "g�ngige",  "g�ngigen",  "g�ngiger",  "g�ngiges",  "g�nzlich",  "haette",  "halb",  "hallo", 
			"hast",  "hattest",  "hattet",  "heraus",  "herein",  "heute",  "heutige",  "hiermit",  "hiesige", 
			"hinein",  "hinten",  "hinterher",  "hoch",  "hundert",  "h�tt",  "h�tte",  "h�tten",  "h�chstens", 
			"igitt",  "immer",  "immerhin",  "important",  "indessen",  "info",  "infolge",  "innen",  "innerhalb", 
			"insofern",  "inzwischen",  "irgend",  "irgendeine",  "irgendwas",  "irgendwen",  "irgendwer", 
			"irgendwie",  "irgendwo",  "je",  "jedenfalls",  "jederlei",  "jedoch",  "jemand",  "jenseits", 
			"j�hrig",  "j�hrige",  "j�hrigen",  "j�hriges",  "kam",  "kannst",  "kaum",  "keines",  "keinerlei", 
			"keineswegs",  "klar",  "klare",  "klaren",  "klares",  "klein",  "kleinen",  "kleiner",  "kleines", 
			"koennen",  "koennt",  "koennte",  "koennten",  "komme",  "kommen",  "kommt",  "konkret",  "konkrete", 
			"konkreten",  "konkreter",  "konkretes",  "konnten",  "k�nn",  "k�nnt",  "k�nnten",  "k�nftig",  "lag", 
			"lagen",  "langsam",  "lassen",  "laut",  "lediglich",  "leer",  "legen",  "legte",  "legten",  "leicht", 
			"leider",  "lesen",  "letze",  "letzten",  "letztendlich",  "letztens",  "letztes",  "letztlich", 
			"lichten",  "liegt",  "liest",  "links",  "l�ngst",  "l�ngstens",  "mag",  "magst",  "mal", 
			"mancherorts",  "manchmal",  "mann",  "margin",  "mehr",  "mehrere",  "meist",  "meiste",  "meisten", 
			"meta",  "mindestens",  "mithin",  "mochte",  "morgen",  "morgige",  "muessen",  "muesst",  "musst", 
			"mussten",  "mu�",  "mu�t",  "m�chte",  "m�chten",  "m�chtest",  "m�gen",  "m�glich",  "m�gliche", 
			"m�glichen",  "m�glicher",  "m�glicherweise",  "m�ssen",  "m�sste",  "m�ssten",  "m��te",  "nachdem", 
			"nacher",  "nachhinein",  "nahm",  "nat�rlich",  "nacht",  "neben",  "nebenan",  "nehmen",  "nein", 
			"neu",  "neue",  "neuem",  "neuen",  "neuer",  "neues",  "neun",  "nie",  "niemals",  "niemand", 
			"nimm",  "nimmer",  "nimmt",  "nirgends",  "nirgendwo",  "nutzen",  "nutzt",  "nutzung",  "n�chste", 
			"n�mlich",  "n�tigenfalls",  "n�tzt",  "oben",  "oberhalb",  "obgleich",  "obschon",  "obwohl",  "oft", 
			"per",  "pfui",  "pl�tzlich",  "pro",  "reagiere",  "reagieren",  "reagiert",  "reagierte",  "rechts", 
			"regelm��ig",  "rief",  "rund",  "sang",  "sangen",  "schlechter",  "schlie�lich",  "schnell",  "schon", 
			"schreibe",  "schreiben",  "schreibens",  "schreiber",  "schwierig",  "sch�tzen",  "sch�tzt", 
			"sch�tzte",  "sch�tzten",  "sechs",  "sect",  "sehrwohl",  "sei",  "seit",  "seitdem",  "seite", 
			"seiten",  "seither",  "selber",  "senke",  "senken",  "senkt",  "senkte",  "senkten",  "setzen", 
			"setzt",  "setzte",  "setzten",  "sicherlich",  "sieben",  "siebte",  "siehe",  "sieht",  "singen", 
			"singt",  "sobald",  "soda�",  "soeben",  "sofern",  "sofort",  "sog",  "sogar",  "solange",  "solc", 
			"hen",  "solch",  "sollen",  "sollst",  "sollt",  "sollten",  "solltest",  "somit",  "sonstwo", 
			"sooft",  "soviel",  "soweit",  "sowie",  "sowohl",  "spielen",  "sp�ter",  "startet",  "startete", 
			"starteten",  "statt",  "stattdessen",  "steht",  "steige",  "steigen",  "steigt",  "stets",  "stieg", 
			"stiegen",  "such",  "suchen",  "s�mtliche",  "tages",  "tat",  "tats�chlich",  "tats�chlichen", 
			"tats�chlicher",  "tats�chliches",  "tausend",  "teile",  "teilen",  "teilte",  "teilten",  "titel", 
			"total",  "trage",  "tragen",  "trotzdem",  "trug",  "tr�gt",  "toll",  "tun",  "tust",  "tut",  "txt", 
			"t�t",  "ueber",  "umso",  "unbedingt",  "ungef�hr",  "unm�glich",  "unm�gliche",  "unm�glichen", 
			"unm�glicher",  "unn�tig",  "unsem",  "unser",  "unsere",  "unserem",  "unseren",  "unserer", 
			"unseres",  "unten",  "unterbrach",  "unterbrechen",  "unterhalb",  "unwichtig",  "usw",  "vergangen", 
			"vergangene",  "vergangener",  "vergangenes",  "vermag",  "vermutlich",  "verm�gen",  "verrate", 
			"verraten",  "verriet",  "verrieten",  "version",  "versorge",  "versorgen",  "versorgt",  "versorgte", 
			"versorgten",  "versorgtes",  "ver�ffentlichen",  "ver�ffentlicher",  "ver�ffentlicht", 
			"ver�ffentlichte",  "ver�ffentlichten",  "ver�ffentlichtes",  "viele",  "vielen",  "vieler",  "vieles", 
			"vielleicht",  "vielmals",  "vier",  "vollst�ndig",  "voran",  "vorbei",  "vorgestern",  "vorher", 
			"vorne",  "vor�ber",  "v�llig",  "w�hrend",  "wachen",  "waere",  "warum",  "weder",  "wegen", 
			"weitere",  "weiterem",  "weiteren",  "weiterer",  "weiteres",  "weiterhin",  "wei�",  "wem",  "wen", 
			"wenig",  "wenige",  "weniger",  "wenigstens",  "wenngleich",  "wer",  "werdet",  "weshalb",  "wessen", 
			"weswegen",  "wichtig",  "wieso",  "wieviel",  "wiewohl",  "willst",  "wirklich",  "wodurch",  "wogegen", 
			"woher",  "wohin",  "wohingegen",  "wohl",  "wohlweislich",  "womit",  "woraufhin",  "woraus",  "worin", 
			"wurde",  "wurden",  "w�hrenddessen",  "w�r",  "w�re",  "w�ren",  "zahlreich",  "zehn",  "zeitweise", 
			"ziehen",  "zieht",  "zog",  "zogen",  "zudem",  "zuerst",  "zufolge",  "zugleich",  "zuletzt",  "zumal", 
			"zur�ck",  "zusammen",  "zuviel",  "zwanzig",  "zwei",  "zw�lf",  "�hnlich", 
			"�bel",  "�berall",  "�berallhin",  "�berdies",  "�bermorgen",  "�brig",  "�brigens"
		);

    public $query;
    public $results = array();
    public $resultTypes = array();
    public $time = 0;
    public $count = 0;
    public $error;
    public $resultsPerPage = 30;
    public $minLength = 4;

    public function query($query, $filter = null) {
        $this->query = $query;
        $this->filter = $filter;
        if (strlen($query) >= $this->minLength) {
            $this->search();
        } else {
            $this->error = _('Der eingegebene Suchbegriff ist zu kurz');
        }
    }

    public function resultPage($page = 0) {
        return array_slice($this->results, $page * $this->resultsPerPage, $this->resultsPerPage);
    }

    private function search() {
        // Timecapture
        $time = microtime(1);

        $statement = $this->getResultSet();
        while ($object = $statement->fetch(PDO::FETCH_ASSOC)) {

            if (!$this->filter || $this->filter == $object['type']) {
                $object['link'] = self::getLink($object);
                $this->results[] = $object;
            }
            $this->resultTypes[$object['type']] ++;
            $this->count++;
        }

        $this->time = microtime(1) - $time;
    }

    private function getResultSet($limit = null) {   
        
        // Find out single words
        $words = explode(' ', $this->query);
        
        // Filter for stopwords
        $words = self::filterStopwords($words);
        
        // Stick em together
        $search = implode('* ', array_merge($words, array('"'.$this->query.'"')));
        
        $statement = DBManager::get()->prepare("SELECT search_object.*,text FROM ("
                . "SELECT object_id,text "
                . "FROM search_index "
                . "WHERE MATCH (text) AGAINST (:query IN BOOLEAN MODE) "
                . "GROUP BY object_id "
                . "ORDER BY SUM(MATCH (text) AGAINST (:query IN BOOLEAN MODE) * relevance) DESC"
                . ") as sr JOIN search_object USING (object_id)" . self::buildWhere() . ($limit ? " LIMIT $limit" : ""));
        $statement->bindParam(':query', $search);
        $statement->bindParam(':user', $GLOBALS['user']->id);
        $statement->execute();
        return $statement;
    }

    public static function buildWhere() {
        if ($GLOBALS['perm']->have_perm('root')) {
            return "";
        }
        return "WHERE visible = 1 OR visible IN (SELECT seminar_id FROM seminar_user WHERE user_id = :user)";
    }

    public static function getTypeName($key) {
        $class = self::getClass($key);
        return $class::getName();
    }

    private static function getClass($type) {
        return "IndexObject_" . ucfirst($type);
    }

    public static function getLink($object) {
        $class = self::getClass($object['type']);
        return $class::link($object);
    }

    public static function getInfo($object, $query) {
        // Cut down if info is to long
        if (strlen($object['text']) > 200) {
            $object['text'] = substr($object['text'], max(array(0, self::findWordPosition($query, $object['text']) - 100)), 200);
        }

        // Split words to get them marked individual
        $words = str_replace(' ', '|', preg_quote($query));

        return preg_replace_callback("/$words/i", function($hit) {
            return "<span class='result'>$hit[0]</span>";
        }, htmlReady($object['text']));
    }

    public function includePath() {
        return __FILE__;
    }

    public function getResults($keyword, $contextual_data = array(), $limit = PHP_INT_MAX, $offset = 0) {

        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            include $indexFile;
        }

        $this->query = $keyword;
        $stmt = $this->getResultSet(10);
        while ($object = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = array($object['object_id'], $object['title']);
        }
        return $result;
    }

    public function getAvatarImageTag($id) {
        $stmt = DBManager::get()->prepare('SELECT * FROM search_object WHERE object_id = ? LIMIT 1');
        $stmt->execute(array($id));
        $object = $stmt->fetch(PDO::FETCH_ASSOC);
        $class = self::getClass($object['type']);
        return $class::getAvatar($object);
    }

    public function getPages($current = 1) {
        return array_slice(range(1, $this->countResultPages() - 1), min(array(max(array(0, $current - 5)), $this->countResultPages() - 10)), 10);
    }

    public function countResultPages() {
        return ceil(count($this->results) / $this->resultsPerPage);
    }
    
    private static function findWordPosition($words, $text) {
        foreach (explode(' ', $words) as $word) {
            $pos = stripos($text, $word);
            if ($pos) {
                return $pos;
            }
        }
    }
    
    private static function filterStopwords($input) {
        $new = $input;
        foreach ($input as $key => $test) {
            if (in_array($test, self::$STOPWORDS)) {
                unset($new[$key]);
                continue;
            }
        }
        if ($new) {
            return $new;
        }
        return $input;
    }

}
