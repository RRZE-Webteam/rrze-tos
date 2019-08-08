# RRZE-TOS

Generator für rechtliche Pflichtangaben auf einem Webauftritt.


## Download 

GITHub-Repo: https://github.com/RRZE-Webteam/rrze-tos/


## Autor 
RRZE-Webteam , http://www.rrze.fau.de

## Copryright

GNU General Public License (GPL) Version 2 


## Zweck 

Der Generator erstellt die im deutschen und europäischen Rechtsraum verbindlichen 
Seiten für ein Impressum, einer Datenschutzerklärung und einer 
Barrierefreiheitserklärung.

Die vorkonfigurierten und optionalen Inhaltstexte und Rechtsnormen beziehen sich hierbei
in der aktuellen Version auf den Rahmen, den Einrichtungen der 
Friedrich-Alexander-Universität Erlangen-Nürnberg (FAU) unterworfen sind.


## Keine Gewährleistung

Es kann und wird keine Gewährleistung und Garantie gegeben auf die rechtliche Korrektheit 
und Aktualität der von diesem Plugin erzeugten Rechtstexte.
Dies gilt insbesondere für Teile der Datenschutzerklärung.



## Endpoints

Dieses WordPress-Plugin erstellt die drei Endpoint-Seiten
- /impressum
- /datenschutz 
- /barrierefreiheit
 
bzw. auf Websites mit englischer Sprache die Endpoints
-  /imprint
-  /privacy
-  /accessibility


## Individuelle Anpassungen

Administratoren von Websites können im Backend unter "Einstellungen" &gt; "Rechtliche Pflichtangaben" 
individuelle Anpasungen an den Texten vornehmen.
Ausserdem können dort die jeweiligen Pflichtdaten (z.B. Angaben zur
verantwortlichen Person) ergänzt werden.


## Anpassungen an andere Universitäten und Einrichtungen für universitäre WordPress-Betreiber

Das Plugin ist gedacht zum Einsatz in einer WordPress-Multisite-Instanz. 
Administratoren von einzelnen WordPress-Instanzen sollen bei solchen Angeboten die vorgegebenen
Texte und Optionen, die in den "Einstellungen" vorgegeben werden, üblicherweise nicht ändern
können.

(Super)Administratoren von Multisite-Instanzen können daher Anpassungen an die
lokalen Gegebenheiten nur durch die Änderung der Template-Dateien im
Order ```templates/content/``` vornehmen.
Dort finden sich alle Templates für die einzelnen, optional einschaltbaren Absätze und 
Endpoint-Seiten.
Die Liste der in den "Einstellungen" konfigurierbaren Settings wird durch den
$settings-Array in der Datei ```includes/Options.php```definiert.
Die Auswahlliste zu den AUfsichtsbehörden aus Deutschland, deren Daten derzeit noch 
nicht vollständig ist, findet sich in selber Datei im dem Array $rechtsraum;



