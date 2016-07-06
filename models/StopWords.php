<?php
/**
 * User: manschwa
 * Date: 06.07.16
 * Time: 13:19
 */

class StopWords
{
    private static $STOPWORDS_GERMAN = array(
        "ab", "bei", "da", "deshalb", "ein", "f�r", "finde", "haben", "hier", "ich", "ja",
        "kann", "machen", "muesste", "nach", "oder", "seid", "sonst", "und", "vom", "wann", "wenn",
        "wie", "zu", "bin", "eines", "hat", "manche", "solches", "an", "anderm", "bis", "das", "deinem",
        "demselben", "dir", "doch", "einig", "er", "eurer", "hatte", "ihnen", "ihre", "ins", "jenen",
        "keinen", "manchem", "meinen", "nichts", "seine", "soll", "unserm", "welche", "werden", "wollte",
        "w�hrend", "alle", "allem", "allen", "aller", "alles", "als", "also", "am", "ander", "andere",
        "anderem", "anderen", "anderer", "anderes", "andern", "anders", "auch", "auf", "aus", "bist",
        "bsp.", "daher", "damit", "dann", "dasselbe", "dazu", "da�", "dein", "deine", "deinen",
        "deiner", "deines", "dem", "den", "denn", "denselben", "der", "derer", "derselbe",
        "derselben", "des", "desselben", "dessen", "dich", "die", "dies", "diese", "dieselbe",
        "dieselben", "diesem", "diesen", "dieser", "dieses", "dort", "du", "durch", "eine", "einem",
        "einen", "einer", "einige", "einigem", "einigen", "einiger", "einiges", "einmal", "es", "etwas",
        "euch", "euer", "eure", "eurem", "euren", "eures", "ganz", "ganze", "ganzen", "ganzer",
        "ganzes", "gegen", "gemacht", "gesagt", "gesehen", "gewesen", "gewollt", "hab", "habe",
        "hatten", "hin", "hinter", "ihm", "ihn", "ihr", "ihrem", "ihren", "ihrer", "ihres",
        "im", "in", "indem", "ist", "jede", "jedem", "jeden", "jeder", "jedes", "jene", "jenem",
        "jener", "jenes", "jetzt", "kein", "keine", "keinem", "keiner", "keines", "konnte", "k�nnten",
        "k�nnen", "k�nnte", "mache", "machst", "macht", "machte", "machten", "man", "manchen", "mancher",
        "manches", "mein", "meine", "meinem", "meiner", "meines", "mich", "mir", "mit", "muss",
        "musste", "m��t", "nicht", "noch", "nun", "nur", "ob", "ohne", "sage", "sagen", "sagt",
        "sagte", "sagten", "sagtest", "sehe", "sehen", "sehr", "seht", "sein", "seinem", "seinen",
        "seiner", "seines", "selbst", "sich", "sicher", "sie", "sind", "so", "solche", "solchem",
        "solchen", "solcher", "sollte", "sondern", "um", "uns", "unse", "unsen", "unser", "unses",
        "unter", "viel", "von", "vor", "war", "waren", "warst", "was", "weg", "weil", "weiter",
        "welchem", "welchen", "welcher", "welches", "welche", "werde", "wieder", "will", "wir", "wird",
        "wirst", "wo", "wolle", "wollen", "wollt", "wollten", "wolltest", "wolltet", "w�rde", "w�rden",
        "z.B.", "zum", "zur", "zwar", "zwischen", "�ber", "aber", "abgerufen", "abgerufene",
        "abgerufener", "abgerufenes", "acht", "allein", "allerdings", "allerlei", "allgemein",
        "allm�hlich", "allzu", "alsbald", "andererseits", "andernfalls", "anerkannt", "anerkannte",
        "anerkannter", "anerkanntes", "anfangen", "anfing", "angefangen", "angesetze", "angesetzt",
        "angesetzten", "angesetzter", "ansetzen", "anstatt", "arbeiten", "aufgeh�rt", "aufgrund",
        "aufh�ren", "aufh�rte", "aufzusuchen", "ausdr�cken", "ausdr�ckt", "ausdr�ckte", "ausgenommen",
        "ausser", "ausserdem", "author", "autor", "au�en", "au�er", "au�erdem", "au�erhalb", "bald",
        "bearbeite", "bearbeiten", "bearbeitete", "bearbeiteten", "bedarf", "bedurfte", "bed�rfen",
        "befragen", "befragte", "befragten", "befragter", "begann", "beginnen", "begonnen", "behalten",
        "behielt", "beide", "beiden", "beiderlei", "beides", "beim", "bei", "beinahe", "beitragen",
        "beitrugen", "bekannt", "bekannte", "bekannter", "bekennen", "benutzt", "bereits", "berichten",
        "berichtet", "berichtete", "berichteten", "besonders", "besser", "bestehen", "besteht",
        "betr�chtlich", "bevor", "bez�glich", "bietet", "bisher", "bislang", "bis", "bleiben",
        "blieb", "bloss", "blo�", "brachte", "brachten", "brauchen", "braucht", "bringen", "br�uchte",
        "bzw", "b�den", "ca.", "dabei", "dadurch", "daf�r", "dagegen", "dahin", "damals", "danach",
        "daneben", "dank", "danke", "danken", "dannen", "daran", "darauf", "daraus", "darf", "darfst",
        "darin", "darum", "darunter", "dar�ber", "dar�berhinaus", "dass", "davon", "davor", "demnach",
        "denen", "dennoch", "derart", "derartig", "derem", "deren", "derjenige", "derjenigen", "derzeit",
        "desto", "deswegen", "diejenige", "diesseits", "dinge", "direkt", "direkte", "direkten",
        "direkter", "doppelt", "dorther", "dorthin", "drauf", "drei", "drei�ig", "drin", "dritte",
        "drunter", "dr�ber", "dunklen", "durchaus", "durfte", "durften", "d�rfen", "d�rfte", "eben",
        "ebenfalls", "ebenso", "ehe", "eher", "eigenen", "eigenes", "eigentlich", "einba�n",
        "einerseits", "einfach", "einf�hren", "einf�hrte", "einf�hrten", "eingesetzt", "einigerma�en",
        "eins", "einseitig", "einseitige", "einseitigen", "einseitiger", "einst", "einstmals", "einzig",
        "ende", "entsprechend", "entweder", "erg�nze", "erg�nzen", "erg�nzte", "erg�nzten", "erhalten",
        "erhielt", "erhielten", "erh�lt", "erneut", "erst", "erste", "ersten", "erster", "er�ffne",
        "er�ffnen", "er�ffnet", "er�ffnete", "er�ffnetes", "etc", "etliche", "etwa", "fall", "falls",
        "fand", "fast", "ferner", "finden", "findest", "findet", "folgende", "folgenden", "folgender",
        "folgendes", "folglich", "fordern", "fordert", "forderte", "forderten", "fortsetzen", "fortsetzt",
        "fortsetzte", "fortsetzten", "fragte", "frau", "frei", "freie", "freier", "freies", "fuer",
        "f�nf", "gab", "ganzem", "gar", "gbr", "geb", "geben", "geblieben", "gebracht", "gedurft",
        "geehrt", "geehrte", "geehrten", "geehrter", "gefallen", "gefiel", "gef�lligst", "gef�llt",
        "gegeben", "gehabt", "gehen", "geht", "gekommen", "gekonnt", "gemocht", "gem�ss", "genommen",
        "genug", "gern", "gestern", "gestrige", "getan", "geteilt", "geteilte", "getragen",
        "gewisserma�en", "geworden", "ggf", "gib", "gibt", "gleich", "gleichwohl", "gleichzeitig",
        "gl�cklicherweise", "gmbh", "gratulieren", "gratuliert", "gratulierte", "gut", "gute", "guten",
        "g�ngig", "g�ngige", "g�ngigen", "g�ngiger", "g�ngiges", "g�nzlich", "haette", "halb", "hallo",
        "hast", "hattest", "hattet", "heraus", "herein", "heute", "heutige", "hiermit", "hiesige",
        "hinein", "hinten", "hinterher", "hoch", "hundert", "h�tt", "h�tte", "h�tten", "h�chstens",
        "igitt", "immer", "immerhin", "important", "indessen", "info", "infolge", "innen", "innerhalb",
        "insofern", "inzwischen", "irgend", "irgendeine", "irgendwas", "irgendwen", "irgendwer",
        "irgendwie", "irgendwo", "je", "jedenfalls", "jederlei", "jedoch", "jemand", "jenseits",
        "j�hrig", "j�hrige", "j�hrigen", "j�hriges", "kam", "kannst", "kaum", "keines", "keinerlei",
        "keineswegs", "klar", "klare", "klaren", "klares", "klein", "kleinen", "kleiner", "kleines",
        "koennen", "koennt", "koennte", "koennten", "komme", "kommen", "kommt", "konkret", "konkrete",
        "konkreten", "konkreter", "konkretes", "konnten", "k�nn", "k�nnt", "k�nnten", "k�nftig", "lag",
        "lagen", "langsam", "lassen", "laut", "lediglich", "leer", "legen", "legte", "legten", "leicht",
        "leider", "lesen", "letze", "letzten", "letztendlich", "letztens", "letztes", "letztlich",
        "lichten", "liegt", "liest", "links", "l�ngst", "l�ngstens", "mag", "magst", "mal",
        "mancherorts", "manchmal", "mann", "margin", "mehr", "mehrere", "meist", "meiste", "meisten",
        "meta", "mindestens", "mithin", "mochte", "morgen", "morgige", "muessen", "muesst", "musst",
        "mussten", "mu�", "mu�t", "m�chte", "m�chten", "m�chtest", "m�gen", "m�glich", "m�gliche",
        "m�glichen", "m�glicher", "m�glicherweise", "m�ssen", "m�sste", "m�ssten", "m��te", "nachdem",
        "nacher", "nachhinein", "nahm", "nat�rlich", "nacht", "neben", "nebenan", "nehmen", "nein",
        "neu", "neue", "neuem", "neuen", "neuer", "neues", "neun", "nie", "niemals", "niemand",
        "nimm", "nimmer", "nimmt", "nirgends", "nirgendwo", "nutzen", "nutzt", "nutzung", "n�chste",
        "n�mlich", "n�tigenfalls", "n�tzt", "oben", "oberhalb", "obgleich", "obschon", "obwohl", "oft",
        "per", "pfui", "pl�tzlich", "pro", "reagiere", "reagieren", "reagiert", "reagierte", "rechts",
        "regelm��ig", "rief", "rund", "sang", "sangen", "schlechter", "schlie�lich", "schnell", "schon",
        "schreibe", "schreiben", "schreibens", "schreiber", "schwierig", "sch�tzen", "sch�tzt",
        "sch�tzte", "sch�tzten", "sechs", "sect", "sehrwohl", "sei", "seit", "seitdem", "seite",
        "seiten", "seither", "selber", "senke", "senken", "senkt", "senkte", "senkten", "setzen",
        "setzt", "setzte", "setzten", "sicherlich", "sieben", "siebte", "siehe", "sieht", "singen",
        "singt", "sobald", "soda�", "soeben", "sofern", "sofort", "sog", "sogar", "solange", "solc",
        "hen", "solch", "sollen", "sollst", "sollt", "sollten", "solltest", "somit", "sonstwo",
        "sooft", "soviel", "soweit", "sowie", "sowohl", "spielen", "sp�ter", "startet", "startete",
        "starteten", "statt", "stattdessen", "steht", "steige", "steigen", "steigt", "stets", "stieg",
        "stiegen", "such", "suchen", "s�mtliche", "tages", "tat", "tats�chlich", "tats�chlichen",
        "tats�chlicher", "tats�chliches", "tausend", "teile", "teilen", "teilte", "teilten", "titel",
        "total", "trage", "tragen", "trotzdem", "trug", "tr�gt", "toll", "tun", "tust", "tut", "txt",
        "t�t", "ueber", "umso", "unbedingt", "ungef�hr", "unm�glich", "unm�gliche", "unm�glichen",
        "unm�glicher", "unn�tig", "unsem", "unser", "unsere", "unserem", "unseren", "unserer",
        "unseres", "unten", "unterbrach", "unterbrechen", "unterhalb", "unwichtig", "usw", "vergangen",
        "vergangene", "vergangener", "vergangenes", "vermag", "vermutlich", "verm�gen", "verrate",
        "verraten", "verriet", "verrieten", "version", "versorge", "versorgen", "versorgt", "versorgte",
        "versorgten", "versorgtes", "ver�ffentlichen", "ver�ffentlicher", "ver�ffentlicht",
        "ver�ffentlichte", "ver�ffentlichten", "ver�ffentlichtes", "viele", "vielen", "vieler", "vieles",
        "vielleicht", "vielmals", "vier", "vollst�ndig", "voran", "vorbei", "vorgestern", "vorher",
        "vorne", "vor�ber", "v�llig", "w�hrend", "wachen", "waere", "warum", "weder", "wegen",
        "weitere", "weiterem", "weiteren", "weiterer", "weiteres", "weiterhin", "wei�", "wem", "wen",
        "wenig", "wenige", "weniger", "wenigstens", "wenngleich", "wer", "werdet", "weshalb", "wessen",
        "weswegen", "wichtig", "wieso", "wieviel", "wiewohl", "willst", "wirklich", "wodurch", "wogegen",
        "woher", "wohin", "wohingegen", "wohl", "wohlweislich", "womit", "woraufhin", "woraus", "worin",
        "wurde", "wurden", "w�hrenddessen", "w�r", "w�re", "w�ren", "zahlreich", "zehn", "zeitweise",
        "ziehen", "zieht", "zog", "zogen", "zudem", "zuerst", "zufolge", "zugleich", "zuletzt", "zumal",
        "zur�ck", "zusammen", "zuviel", "zwanzig", "zwei", "zw�lf", "�hnlich",
        "�bel", "�berall", "�berallhin", "�berdies", "�bermorgen", "�brig", "�brigens"
    );
    
    private static $STOPWORDS_ENGLISH = array(
        "a" ,"about" ,"above" ,"after" ,"again" ,"against" ,"all" ,"am" ,"an" ,"and" ,"any" ,"are" ,
        "aren't" ,"as" ,"at" ,"be" ,"because" ,"been" ,"before" ,"being" ,"below" ,"between" ,"both" ,
        "but" ,"by" ,"can't" ,"cannot" ,"could" ,"couldn't" ,"did" ,"didn't" ,"do" ,"does" ,"doesn't" ,
        "doing" ,"don't" ,"down" ,"during" ,"each" ,"few" ,"for" ,"from" ,"further" ,"had" ,"hadn't" ,
        "has" ,"hasn't" ,"have" ,"haven't" ,"having" ,"he" ,"he'd" ,"he'll" ,"he's" ,"her" ,"here" ,
        "here's" ,"hers" ,"herself" ,"him" ,"himself" ,"his" ,"how" ,"how's" ,"i" ,"i'd" ,"i'll" ,"i'm" ,
        "i've" ,"if" ,"in" ,"into" ,"is" ,"isn't" ,"it" ,"it's" ,"its" ,"itself" ,"let's" ,"me" ,"more" ,
        "most" ,"mustn't" ,"my" ,"myself" ,"no" ,"nor" ,"not" ,"of" ,"off" ,"on" ,"once" ,"only" ,"or" ,
        "other" ,"ought" ,"our" ,"ours" ,"ourselves" ,"out" ,"over" ,"own" ,"same" ,"shan't" ,"she" ,
        "she'd" ,"she'll" ,"she's" ,"should" ,"shouldn't" ,"so" ,"some" ,"such" ,"than" ,"that" ,"that's" ,
        "the" ,"their" ,"theirs" ,"them" ,"themselves" ,"then" ,"there" ,"there's" ,"these" ,"they" ,"they'd" ,
        "they'll" ,"they're" ,"they've" ,"this" ,"those" ,"through" ,"to" ,"too" ,"under" ,"until" ,"up" ,
        "very" ,"was" ,"wasn't" ,"we" ,"we'd" ,"we'll" ,"we're" ,"we've" ,"were" ,"weren't" ,"what" ,
        "what's" ,"when" ,"when's" ,"where" ,"where's" ,"which" ,"while" ,"who" ,"who's" ,"whom" ,"why" ,
        "why's" ,"with" ,"won't" ,"would" ,"wouldn't" ,"you" ,"you'd" ,"you'll" ,"you're" ,"you've" ,
        "your" ,"yours" ,"yourself" ,"yourselves"
        );

    /**
     * @return array|string
     */
    public static function getStopWords()
    {
        $lang = getUserLanguage($GLOBALS['user']->id);
        switch ($lang) {
            case 'de_DE':
                return self::getGermanStopWords();
                break;
            case 'en_GB':
                return self::getEnglishStopWords();
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * @return array with german stop words.
     */
    private static function getGermanStopWords()
    {
        return self::$STOPWORDS_GERMAN;
    }

    /**
     * @return array
     */
    private static function getEnglishStopWords()
    {
        return self::$STOPWORDS_ENGLISH;
    }
}