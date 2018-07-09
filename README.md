# Das WCAG-Plugin
Mit dem WCAG-Plugin wird nach der Aktivierung automatisch eine Barrierefreiheitserklärung eingefügt. Diese kann über einen Link im Footer aufgerufen werden.

## Aufbau des Plugins
Das Plugin gliedert sich in mehrere Teile und wird über einen Endpoint in die Webseite eingefügt.

1. Nach der Aktivierung erscheint der Menüpunkt Barrierefreiheit (Accessibility) in den Einstellungen. Hier können verschiedene Einstellungen gemacht werden (z.B. Werden die WCAG-Kriterien derzeit erfüllt?). Darüber hinaus werden die Kontaktdaten der Verantwortlichen für die Webseite im FAU-Netz automatisch ausgefüllt und können ergänzt werden. Die Url für das RRZE ist: https://www.wmp.rrze.fau.de/api/domain/metadata/www.rrze.fau.de. Wird das Plugin außerhalb der Universität verwendet, so müssen die Kontaktdaten händisch in den Einstellungen eingepflegt werden.

2. Darüber hinaus beinhaltet das Plugin ein Formular, welches per Shortcode im jeweiligen Template eingebunden wird. Aufbau des Shortcodes:
```
[contact field-name="class,type,id" field-email="" ...]
```

Da name und email in Wordpress geschützte Begriffe sind, wurde für diese eine Zusatzklasse (rrze-name und rrze-email) hinzugefügt.

### Contact-Shortcode für das WCAG-Plugin

```
[contact 

Allgemeine Formularfelder
---

field-name="name,text,name-id,rrze-name"
field-email="email,text,email-id,rrze-email" 
field-feedback="feedback,textarea,textarea-id" 
...

Captcha Felder
---

field-captcha="captcha,text,captcha-id" 
field-answer="answer,hidden,hidden-id" 

Timeout in Sekunden - wird das Formular schneller ausgefüllt als der Timeout (Sekunden) erfolgt keine Übermittlung der Formulardaten
----

field-timeout="timeout,hidden,timeout-id"
]
```
3. Support für die FAU-Themes, für das Events-Theme sowie für das RRZE-Theme.

4. Links zur EU-Richtline sowie zur Schiedsstelle wurden eingefügt.

5. Language Support für Englisch gegeben. Die deutschen Begriffe wurden ins Englische übersetzt und in einer englischsprachigen WP-Instanz getestet.