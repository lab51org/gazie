# GAzie - Gestione Aziendale

![GitHub release (latest SemVer including pre-releases)](https://img.shields.io/github/v/release/lab51org/gazie?include_prereleases&sort=semver)
![SourceForge](https://img.shields.io/sourceforge/dt/gazie)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)
![GitHub](https://img.shields.io/github/license/lab51org/gazie)
[![License: CC BY-SA 4.0](https://img.shields.io/badge/License-CC%20BY--SA%204.0-lightgrey.svg)](https://creativecommons.org/licenses/by-sa/4.0/)

[![Download GAzie - Gestione Aziendale](https://a.fsdn.com/con/app/sf-download-button)](https://sourceforge.net/projects/gazie/files/latest/download)
## Sommario

- [GAzie - Gestione Aziendale](#gazie---gestione-aziendale)
    * [Che cos'è GAzie](#che-cos-gazie)
    * [TL;DR](#tldr)
        + [Sei un webmaster?](#sei-un-webmaster)
        + [Sei uno sviluppatore?](#sei-uno-sviluppatore)
        + [Requisiti di Sistema](#requisiti-di-sistema)
        + [Installazione Veloce](#installazione-veloce)
    * [A chi è rivolto](#a-chi--rivolto)
    * [Che cos'è in grado di fare](#che-cos-in-grado-di-fare)
        + [Ciclo attivo](#ciclo-attivo)
        + [Ciclo Passivo](#ciclo-passivo)
        + [Magazzino e Produzione](#magazzino-e-produzione)
    * [Funzionalità ulteriori - Moduli Specifici](#funzionalit-ulteriori---moduli-specifici)
    * [Documentazione e Supporto](#documentazione-e-supporto)
        + [Utente Finale](#utente-finale--webmaster)
        + [Sviluppatori](#sviluppatori)
        + [Licenza](#licenza)
    * [Informazioni sulle versioni](#informazioni-sulle-versioni) 
    * [To Do](#to-do)

## Che cos'è GAzie
GAzie è un software gestionale (ERP) multiazienda in grado di gestire tanti aspetti dell'azienda, dalla gestione ordini, alla produzione, al magazzino a lotti, la contabilità, il registratore di cassa, le fatture elettroniche attive e passive, il quaderno di campagna, il registro SIAN, la Sincronizzazione con e-commerce.
Un gestionale completo per le PMI, scritto in PHP e base di dati database MySQL/MariaDB.

## TL;DR
### Sei un webmaster?

Scarica l'ultima versione di GAzie [qui](https://sourceforge.net/projects/gazie/files/gazie/8.00/gazie8.00.zip/download)

### Sei uno sviluppatore?
Se vuoi contribuire allo sviluppo di GAzie, ti chiediamo lavorare con SVN (devi avere un account su SourceForge.net):

`svn checkout --username=[tuo username su SF] svn+ssh://[tuo username su SF]@svn.code.sf.net/p/gazie/code/ gazie-code`

A Digiuno di SVN? leggi questa guida: [https://www.html.it/guide/guida-subversion/](https://www.html.it/guide/guida-subversion/)

Se invece vuoi semplicemente fare un fork e apportare in privato le tue modifiche
puoi clonare il repository direttamente da svn in sola lettura oppure da GitHUB [1]:

`svn checkout svn://svn.code.sf.net/p/gazie/code/ gazie-code`

`git clone https://github.com/lab51org/gazie.git gazie-code`

Oppure, se ti serve una release specifica senza portarti dietro tutte le modifiche dalla prima versione:

`git clone --depth 1 --branch [tag_name] https://github.com/lab51org/gazie.git gazie-code`
 
[1]: Il repository su GitHUB è solo in lettura, è sempre aggiornato ma non è ufficiale e non accetta pull request.

### Requisiti di Sistema

* Webserver Apache 2.4 o superiore, IIS su windows
* Versione PHP >= 7.4 compilata DSO, non c'è ancora il supporto per PHP-FPM
* Estensioni PHP richieste: MySQLi
* Database MariaDB o MySQL (consigliato MariaDB 10.x.x o sup.)


### Installazione Veloce
Versione attuale: Versione 8.00, Note di Release a questo [link](https://github.com/lab51org/gazie/releases/tag/8.00)

1) `unzip gazie8.00.zip`
2) `cd gazie/setup`
3) `mysqladmin create gazie`
4) `mysql -u[user] -p [nome DB] < install_x.x.x.sql`

- Per fare aggiornamento della versione:
1) `mysql -u[user] -p [nome DB] < update_to_x.x.x.sql`



## A chi è rivolto
GAzie è la soluzione ideale per piccole aziende che operano nel commercio, nell'industria, nei servizi e nell'agricoltura

## Che cos'è in grado di fare
### Ciclo attivo
* Gestione clienti
* Preventivi
* Ordini
* Fatture immediate, differite, note di credito
* Generazione file xml per fattura elettronica

### Ciclo Passivo
* Gestione fornitori
* Preventivi, Ordini, DDT, Resi, Fatture fornitori
* Acquisizione Fatture elettroniche passive
* Contabilizzazione delle fatture in Prima Nota
* Gestione dei Cespiti
* Registro IVA e Libro Giornale
* Riconciliazione E/C in contabilità

### Magazzino e Produzione
* Gestione del Magazzino a lotti con Magazzini multipli
* Produzione di base

## Funzionalità ulteriori - Moduli Specifici
* Quaderno di campagna per le aziende agricole
* Gestione del SIAN per olivicoltori e confezionatori
* Registratore di cassa
* Sincronizzazione con i principali sistemi di E-commerce

## Documentazione e Supporto
### Utente Finale / Webmaster

Per l'installazione seguire [la guida sopra](#tldr) oppure dare una letta al file [INSTALL.md](docs/INSTALL.md)
È disponibile un Help in linea all'interno del programma e il supporto della community a questo [link](https://sourceforge.net/projects/gazie/support)

### Sviluppatori
Maggiori dettagli nella cartella `dev` e nel [wiki del progetto su SF](https://sourceforge.net/p/gazie/wiki) (di prossimo aggiornamento, vedi [to do](#to-do))

### Licenza
Trovi una copia della licenza dentro la cartella `doc`, oppure nel file [LICENSE](./LICENSE.md)

## Informazioni sulle versioni

Qui di seguito sono elencate le email di presentazioni delle versioni di GAzie pubblicate fin'ora.

Clicca sul file della specifica versione per leggerne i dettagli e le funzionalità introdotte

| Versione 7.00 - 7.19 | Versione 7.20 - 7.39 | Versione 7.40 - 8.00 |
| --- | --- | --- |
| [7.00](changelogs/mail_presentazione/mail_pres700.txt) | [7.20](changelogs/mail_presentazione/mail_pres720.txt) | [7.40](changelogs/mail_presentazione/mail_pres740.txt) |
| [7.01](changelogs/mail_presentazione/mail_pres701.txt) | [7.21](changelogs/mail_presentazione/mail_pres721.txt) | [7.41](changelogs/mail_presentazione/mail_pres741.txt) |
| [7.02](changelogs/mail_presentazione/mail_pres702.txt) | [7.22](changelogs/mail_presentazione/mail_pres722.txt) | [7.42](changelogs/mail_presentazione/mail_pres742.txt) |
| [7.03](changelogs/mail_presentazione/mail_pres703.txt) | [7.23](changelogs/mail_presentazione/mail_pres723.txt) | [7.43](changelogs/mail_presentazione/mail_pres743.txt) |
| [7.04](changelogs/mail_presentazione/mail_pres704.txt) | [7.24](changelogs/mail_presentazione/mail_pres724.txt) | [7.44](changelogs/mail_presentazione/mail_pres744.txt) |
| [7.05](changelogs/mail_presentazione/mail_pres705.txt) | [7.25](changelogs/mail_presentazione/mail_pres725.txt) | [7.45](changelogs/mail_presentazione/mail_pres745.txt) |
| [7.06](changelogs/mail_presentazione/mail_pres706.txt) | [7.26](changelogs/mail_presentazione/mail_pres726.txt) | [7.46](changelogs/mail_presentazione/mail_pres746.txt) |
| [7.07](changelogs/mail_presentazione/mail_pres707.txt) | [7.27](changelogs/mail_presentazione/mail_pres727.txt) | [7.47](changelogs/mail_presentazione/mail_pres747.txt) |
| [7.08](changelogs/mail_presentazione/mail_pres708.txt) | [7.28](changelogs/mail_presentazione/mail_pres728.txt) | [7.48](changelogs/mail_presentazione/mail_pres748.txt) |
| [7.09](changelogs/mail_presentazione/mail_pres709.txt) | [7.29](changelogs/mail_presentazione/mail_pres729.txt) | [8.00](changelogs/mail_presentazione/mail_pres800.txt) |
| [7.10](changelogs/mail_presentazione/mail_pres710.txt) | [7.30](changelogs/mail_presentazione/mail_pres730.txt) ||
| [7.11](changelogs/mail_presentazione/mail_pres711.txt) | [7.31](changelogs/mail_presentazione/mail_pres731.txt) ||
| [7.12](changelogs/mail_presentazione/mail_pres712.txt) | [7.32](changelogs/mail_presentazione/mail_pres732.txt) ||
| [7.13](changelogs/mail_presentazione/mail_pres713.txt) | [7.33](changelogs/mail_presentazione/mail_pres733.txt) ||
| [7.14](changelogs/mail_presentazione/mail_pres714.txt) | [7.34](changelogs/mail_presentazione/mail_pres734.txt) ||
| [7.15](changelogs/mail_presentazione/mail_pres715.txt) | [7.35](changelogs/mail_presentazione/mail_pres735.txt) ||
| [7.16](changelogs/mail_presentazione/mail_pres716.txt) | [7.36](changelogs/mail_presentazione/mail_pres736.txt) ||
| [7.17](changelogs/mail_presentazione/mail_pres717.txt) | [7.37](changelogs/mail_presentazione/mail_pres737.txt) ||
| [7.18](changelogs/mail_presentazione/mail_pres718.txt) | [7.38](changelogs/mail_presentazione/mail_pres738.txt) ||
| [7.19](changelogs/mail_presentazione/mail_pres719.txt) | [7.39](changelogs/mail_presentazione/mail_pres739.txt) ||


### To Do

- [x] Riscrivere il README più leggibile
- [ ] migliorare e aggiornare manuale installazione
- [ ] Creare guida sviluppatori più dettagliata su wiki di SF
