<?php

class IntelligentSearch extends SearchType {
    
    private static $STOPWORDS = array(
			"ab",  "bei",  "da",  "deshalb",  "ein",  "für",  "finde",  "haben",  "hier",  "ich",  "ja", 
			"kann",  "machen",  "muesste",  "nach",  "oder",  "seid",  "sonst",  "und",  "vom",  "wann",  "wenn", 
			"wie",  "zu",  "bin",  "eines",  "hat",  "manche",  "solches",  "an",  "anderm",  "bis",  "das",  "deinem", 
			"demselben",  "dir",  "doch",  "einig",  "er",  "eurer",  "hatte",  "ihnen",  "ihre",  "ins",  "jenen", 
			"keinen",  "manchem",  "meinen",  "nichts",  "seine",  "soll",  "unserm",  "welche",  "werden",  "wollte", 
			"während",  "alle",  "allem",  "allen",  "aller",  "alles",  "als",  "also",  "am",  "ander",  "andere", 
			"anderem",  "anderen",  "anderer",  "anderes",  "andern",  "anders",  "auch",  "auf",  "aus",  "bist", 
			"bsp.",  "daher",  "damit",  "dann",  "dasselbe",  "dazu",  "daß",  "dein",  "deine",  "deinen", 
			"deiner",  "deines",  "dem",  "den",  "denn",  "denselben",  "der",  "derer",  "derselbe", 
			"derselben",  "des",  "desselben",  "dessen",  "dich",  "die",  "dies",  "diese",  "dieselbe", 
			"dieselben",  "diesem",  "diesen",  "dieser",  "dieses",  "dort",  "du",  "durch",  "eine",  "einem", 
			"einen",  "einer",  "einige",  "einigem",  "einigen",  "einiger",  "einiges",  "einmal",  "es",  "etwas", 
			"euch",  "euer",  "eure",  "eurem",  "euren",  "eures",  "ganz",  "ganze",  "ganzen",  "ganzer", 
			"ganzes",  "gegen",  "gemacht",  "gesagt",  "gesehen",  "gewesen",  "gewollt",  "hab",  "habe", 
			"hatten",  "hin",  "hinter",  "ihm",  "ihn",  "ihr",  "ihrem",  "ihren",  "ihrer",  "ihres", 
			"im",  "in",  "indem",  "ist",  "jede",  "jedem",  "jeden",  "jeder",  "jedes",  "jene",  "jenem", 
			"jener",  "jenes",  "jetzt",  "kein",  "keine",  "keinem",  "keiner",  "keines",  "konnte",  "könnten", 
			"können",  "könnte",  "mache",  "machst",  "macht",  "machte",  "machten",  "man",  "manchen",  "mancher", 
			"manches",  "mein",  "meine",  "meinem",  "meiner",  "meines",  "mich",  "mir",  "mit",  "muss", 
			"musste",  "müßt",  "nicht",  "noch",  "nun",  "nur",  "ob",  "ohne",  "sage",  "sagen",  "sagt", 
			"sagte",  "sagten",  "sagtest",  "sehe",  "sehen",  "sehr",  "seht",  "sein",  "seinem",  "seinen", 
			"seiner",  "seines",  "selbst",  "sich",  "sicher",  "sie",  "sind",  "so",  "solche",  "solchem", 
			"solchen",  "solcher",  "sollte",  "sondern",  "um",  "uns",  "unse",  "unsen",  "unser",  "unses", 
			"unter",  "viel",  "von",  "vor",  "war",  "waren",  "warst",  "was",  "weg",  "weil",  "weiter", 
			"welchem",  "welchen",  "welcher",  "welches",  "welche",  "werde",  "wieder",  "will",  "wir",  "wird", 
			"wirst",  "wo",  "wolle",  "wollen",  "wollt",  "wollten",  "wolltest",  "wolltet",  "würde",  "würden", 
			"z.B.",  "zum",  "zur",  "zwar",  "zwischen",  "über",  "aber",  "abgerufen",  "abgerufene", 
			"abgerufener",  "abgerufenes",  "acht",  "allein",  "allerdings",  "allerlei",  "allgemein", 
			"allmählich",  "allzu",  "alsbald",  "andererseits",  "andernfalls",  "anerkannt",  "anerkannte", 
			"anerkannter",  "anerkanntes",  "anfangen",  "anfing",  "angefangen",  "angesetze",  "angesetzt", 
			"angesetzten",  "angesetzter",  "ansetzen",  "anstatt",  "arbeiten",  "aufgehört",  "aufgrund", 
			"aufhören",  "aufhörte",  "aufzusuchen",  "ausdrücken",  "ausdrückt",  "ausdrückte",  "ausgenommen", 
			"ausser",  "ausserdem",  "author",  "autor",  "außen",  "außer",  "außerdem",  "außerhalb",  "bald", 
			"bearbeite",  "bearbeiten",  "bearbeitete",  "bearbeiteten",  "bedarf",  "bedurfte",  "bedürfen", 
			"befragen",  "befragte",  "befragten",  "befragter",  "begann",  "beginnen",  "begonnen",  "behalten", 
			"behielt",  "beide",  "beiden",  "beiderlei",  "beides",  "beim",  "bei",  "beinahe",  "beitragen", 
			"beitrugen",  "bekannt",  "bekannte",  "bekannter",  "bekennen",  "benutzt",  "bereits",  "berichten", 
			"berichtet",  "berichtete",  "berichteten",  "besonders",  "besser",  "bestehen",  "besteht", 
			"beträchtlich",  "bevor",  "bezüglich",  "bietet",  "bisher",  "bislang",  "bis",  "bleiben", 
			"blieb",  "bloss",  "bloß",  "brachte",  "brachten",  "brauchen",  "braucht",  "bringen",  "bräuchte", 
			"bzw",  "böden",  "ca.",  "dabei",  "dadurch",  "dafür",  "dagegen",  "dahin",  "damals",  "danach", 
			"daneben",  "dank",  "danke",  "danken",  "dannen",  "daran",  "darauf",  "daraus",  "darf",  "darfst", 
			"darin",  "darum",  "darunter",  "darüber",  "darüberhinaus",  "dass",  "davon",  "davor",  "demnach", 
			"denen",  "dennoch",  "derart",  "derartig",  "derem",  "deren",  "derjenige",  "derjenigen",  "derzeit", 
			"desto",  "deswegen",  "diejenige",  "diesseits",  "dinge",  "direkt",  "direkte",  "direkten", 
			"direkter",  "doppelt",  "dorther",  "dorthin",  "drauf",  "drei",  "dreißig",  "drin",  "dritte", 
			"drunter",  "drüber",  "dunklen",  "durchaus",  "durfte",  "durften",  "dürfen",  "dürfte",  "eben", 
			"ebenfalls",  "ebenso",  "ehe",  "eher",  "eigenen",  "eigenes",  "eigentlich",  "einbaün", 
			"einerseits",  "einfach",  "einführen",  "einführte",  "einführten",  "eingesetzt",  "einigermaßen", 
			"eins",  "einseitig",  "einseitige",  "einseitigen",  "einseitiger",  "einst",  "einstmals",  "einzig", 
			"ende",  "entsprechend",  "entweder",  "ergänze",  "ergänzen",  "ergänzte",  "ergänzten",  "erhalten", 
			"erhielt",  "erhielten",  "erhält",  "erneut",  "erst",  "erste",  "ersten",  "erster",  "eröffne", 
			"eröffnen",  "eröffnet",  "eröffnete",  "eröffnetes",  "etc",  "etliche",  "etwa",  "fall",  "falls", 
			"fand",  "fast",  "ferner",  "finden",  "findest",  "findet",  "folgende",  "folgenden",  "folgender", 
			"folgendes",  "folglich",  "fordern",  "fordert",  "forderte",  "forderten",  "fortsetzen",  "fortsetzt", 
			"fortsetzte",  "fortsetzten",  "fragte",  "frau",  "frei",  "freie",  "freier",  "freies",  "fuer", 
			"fünf",  "gab",  "ganzem",  "gar",  "gbr",  "geb",  "geben",  "geblieben",  "gebracht",  "gedurft", 
			"geehrt",  "geehrte",  "geehrten",  "geehrter",  "gefallen",  "gefiel",  "gefälligst",  "gefällt", 
			"gegeben",  "gehabt",  "gehen",  "geht",  "gekommen",  "gekonnt",  "gemocht",  "gemäss",  "genommen", 
			"genug",  "gern",  "gestern",  "gestrige",  "getan",  "geteilt",  "geteilte",  "getragen", 
			"gewissermaßen",  "geworden",  "ggf",  "gib",  "gibt",  "gleich",  "gleichwohl",  "gleichzeitig", 
			"glücklicherweise",  "gmbh",  "gratulieren",  "gratuliert",  "gratulierte",  "gut",  "gute",  "guten", 
			"gängig",  "gängige",  "gängigen",  "gängiger",  "gängiges",  "gänzlich",  "haette",  "halb",  "hallo", 
			"hast",  "hattest",  "hattet",  "heraus",  "herein",  "heute",  "heutige",  "hiermit",  "hiesige", 
			"hinein",  "hinten",  "hinterher",  "hoch",  "hundert",  "hätt",  "hätte",  "hätten",  "höchstens", 
			"igitt",  "immer",  "immerhin",  "important",  "indessen",  "info",  "infolge",  "innen",  "innerhalb", 
			"insofern",  "inzwischen",  "irgend",  "irgendeine",  "irgendwas",  "irgendwen",  "irgendwer", 
			"irgendwie",  "irgendwo",  "je",  "jedenfalls",  "jederlei",  "jedoch",  "jemand",  "jenseits", 
			"jährig",  "jährige",  "jährigen",  "jähriges",  "kam",  "kannst",  "kaum",  "keines",  "keinerlei", 
			"keineswegs",  "klar",  "klare",  "klaren",  "klares",  "klein",  "kleinen",  "kleiner",  "kleines", 
			"koennen",  "koennt",  "koennte",  "koennten",  "komme",  "kommen",  "kommt",  "konkret",  "konkrete", 
			"konkreten",  "konkreter",  "konkretes",  "konnten",  "könn",  "könnt",  "könnten",  "künftig",  "lag", 
			"lagen",  "langsam",  "lassen",  "laut",  "lediglich",  "leer",  "legen",  "legte",  "legten",  "leicht", 
			"leider",  "lesen",  "letze",  "letzten",  "letztendlich",  "letztens",  "letztes",  "letztlich", 
			"lichten",  "liegt",  "liest",  "links",  "längst",  "längstens",  "mag",  "magst",  "mal", 
			"mancherorts",  "manchmal",  "mann",  "margin",  "mehr",  "mehrere",  "meist",  "meiste",  "meisten", 
			"meta",  "mindestens",  "mithin",  "mochte",  "morgen",  "morgige",  "muessen",  "muesst",  "musst", 
			"mussten",  "muß",  "mußt",  "möchte",  "möchten",  "möchtest",  "mögen",  "möglich",  "mögliche", 
			"möglichen",  "möglicher",  "möglicherweise",  "müssen",  "müsste",  "müssten",  "müßte",  "nachdem", 
			"nacher",  "nachhinein",  "nahm",  "natürlich",  "nacht",  "neben",  "nebenan",  "nehmen",  "nein", 
			"neu",  "neue",  "neuem",  "neuen",  "neuer",  "neues",  "neun",  "nie",  "niemals",  "niemand", 
			"nimm",  "nimmer",  "nimmt",  "nirgends",  "nirgendwo",  "nutzen",  "nutzt",  "nutzung",  "nächste", 
			"nämlich",  "nötigenfalls",  "nützt",  "oben",  "oberhalb",  "obgleich",  "obschon",  "obwohl",  "oft", 
			"per",  "pfui",  "plötzlich",  "pro",  "reagiere",  "reagieren",  "reagiert",  "reagierte",  "rechts", 
			"regelmäßig",  "rief",  "rund",  "sang",  "sangen",  "schlechter",  "schließlich",  "schnell",  "schon", 
			"schreibe",  "schreiben",  "schreibens",  "schreiber",  "schwierig",  "schätzen",  "schätzt", 
			"schätzte",  "schätzten",  "sechs",  "sect",  "sehrwohl",  "sei",  "seit",  "seitdem",  "seite", 
			"seiten",  "seither",  "selber",  "senke",  "senken",  "senkt",  "senkte",  "senkten",  "setzen", 
			"setzt",  "setzte",  "setzten",  "sicherlich",  "sieben",  "siebte",  "siehe",  "sieht",  "singen", 
			"singt",  "sobald",  "sodaß",  "soeben",  "sofern",  "sofort",  "sog",  "sogar",  "solange",  "solc", 
			"hen",  "solch",  "sollen",  "sollst",  "sollt",  "sollten",  "solltest",  "somit",  "sonstwo", 
			"sooft",  "soviel",  "soweit",  "sowie",  "sowohl",  "spielen",  "später",  "startet",  "startete", 
			"starteten",  "statt",  "stattdessen",  "steht",  "steige",  "steigen",  "steigt",  "stets",  "stieg", 
			"stiegen",  "such",  "suchen",  "sämtliche",  "tages",  "tat",  "tatsächlich",  "tatsächlichen", 
			"tatsächlicher",  "tatsächliches",  "tausend",  "teile",  "teilen",  "teilte",  "teilten",  "titel", 
			"total",  "trage",  "tragen",  "trotzdem",  "trug",  "trägt",  "toll",  "tun",  "tust",  "tut",  "txt", 
			"tät",  "ueber",  "umso",  "unbedingt",  "ungefähr",  "unmöglich",  "unmögliche",  "unmöglichen", 
			"unmöglicher",  "unnötig",  "unsem",  "unser",  "unsere",  "unserem",  "unseren",  "unserer", 
			"unseres",  "unten",  "unterbrach",  "unterbrechen",  "unterhalb",  "unwichtig",  "usw",  "vergangen", 
			"vergangene",  "vergangener",  "vergangenes",  "vermag",  "vermutlich",  "vermögen",  "verrate", 
			"verraten",  "verriet",  "verrieten",  "version",  "versorge",  "versorgen",  "versorgt",  "versorgte", 
			"versorgten",  "versorgtes",  "veröffentlichen",  "veröffentlicher",  "veröffentlicht", 
			"veröffentlichte",  "veröffentlichten",  "veröffentlichtes",  "viele",  "vielen",  "vieler",  "vieles", 
			"vielleicht",  "vielmals",  "vier",  "vollständig",  "voran",  "vorbei",  "vorgestern",  "vorher", 
			"vorne",  "vorüber",  "völlig",  "während",  "wachen",  "waere",  "warum",  "weder",  "wegen", 
			"weitere",  "weiterem",  "weiteren",  "weiterer",  "weiteres",  "weiterhin",  "weiß",  "wem",  "wen", 
			"wenig",  "wenige",  "weniger",  "wenigstens",  "wenngleich",  "wer",  "werdet",  "weshalb",  "wessen", 
			"weswegen",  "wichtig",  "wieso",  "wieviel",  "wiewohl",  "willst",  "wirklich",  "wodurch",  "wogegen", 
			"woher",  "wohin",  "wohingegen",  "wohl",  "wohlweislich",  "womit",  "woraufhin",  "woraus",  "worin", 
			"wurde",  "wurden",  "währenddessen",  "wär",  "wäre",  "wären",  "zahlreich",  "zehn",  "zeitweise", 
			"ziehen",  "zieht",  "zog",  "zogen",  "zudem",  "zuerst",  "zufolge",  "zugleich",  "zuletzt",  "zumal", 
			"zurück",  "zusammen",  "zuviel",  "zwanzig",  "zwei",  "zwölf",  "ähnlich", 
			"übel",  "überall",  "überallhin",  "überdies",  "übermorgen",  "übrig",  "übrigens"
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
        var_dump($filter);
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

            if (!$this->filter || in_array($object['type'], $this->filter)) {
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
        $statement->execute();
        return $statement;
    }

    public static function buildWhere() {
        if ($GLOBALS['perm']->have_perm('root')) {
            return "";
        }
        foreach (glob(__DIR__ . '/IndexObject_*') as $indexFile) {
            $indexClass = basename($indexFile, ".php");
            $typename = explode('_', $indexClass);
            $typename = strtolower($typename[1]);
            if (method_exists($indexClass, 'getCondition')) {
                $condititions[] = " (search_object.type = '$typename' AND " . $indexClass::getCondition() . ") ";
            } else {
                $condititions[] = " (search_object.type = '$typename') ";
            }
        }
        return " WHERE " . join(' OR ', $condititions);
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
