# InvoicePlane – Eigene Anpassungen

Dieses Repository enthält alle lokalen Änderungen an der InvoicePlane-Installation auf davidt.de, mit Fokus auf PDF-Layouts, Styling und Kompatibilität mit Updates und deinem Workflow.

---

## 💼 Projektziel

- Updatesichere Anpassung der Rechnungsvorlagen im DIN 5008-Format
- Einheitliches Branding (Farben, Logo, Schrift)
- Integration von QR-Code-Zahlungen und Online-Payment via Stripe
- Verbesserung der Gastansicht und PDF-Erstellung für Kunden

---

## 📁 Struktur

```text
application/
└── views/
    └── invoice_templates/
        ├── pdf/
        │   ├── InvoicePlane_DIN5008B_davidt.php          ← Aktives PDF-Template
        │   └── InvoicePlane_DIN5008B_davidt-paid.php     ← Variante mit "Bezahlt"-Hinweis
        └── public/
            └── InvoicePlane_Web-davidt.php               ← Aktives Web-Template

assets/
└── core/css/
    ├── custom.css                                       ← Updatesichere Web-Designanpassungen
    └── custom-pdf.css                                   ← Updatesichere PDF-Designanpassungen
```

---

## 🖋️ Inhaltliche Anpassungen

### PDF-Templates

- DIN 5008 B-konformes Layout für Fensterkuverts
- Absender im Sichtfenster
- Kundendaten auf Position laut Norm
- Flexibler Abstand zum Titelbereich durch QR-Code-Logik
- Klar strukturierter Kopfbereich mit:
  - Betreff
  - Kundennummer
  - Rechnungsnummer
  - Leistungszeitraum
  - Rechnungsdatum

### Fußzeile (Footer)

- Dreispaltig mit:
  - Firmendaten & Inhaber
  - Kontaktinformationen
  - Bankverbindung inkl. IBAN/BIC
- Statische Inhalte vollständig entfernt
- Design an Papierkram angelehnt

### Zahlungshinweise

- Unterschiedliche Hinweise bei offenen vs. bezahlten Rechnungen
- QR-Code wird nur bei offener Summe angezeigt
- Online-Zahlung verlinkt zur InvoicePlane-Gastansicht mit Stripe

### Styling

- Verwendung von **Open Sans** wie auf davidt.de
- Akzentfarbe: `#668100` (laut CI)
- Tabellenköpfe in hellgrau (`#f5f5f5`)
- Hintergrund für Zeilen entfernt
- Schriftgrößen an DIN-Richtlinien angepasst

---

## 🛠 Technisches

### PDF/A-Kompatibilität

- Keine echten Watermarks (`<watermarktext>`) verwendet
- "Bezahlt"-Hinweis bei `invoice_balance <= 0` als normaler Textblock oder Bild

### QR-Code-Anzeige

```php
if ($invoice->invoice_balance >= 0.01) {
  // Anzeige mit invoice_qrcode()
} else {
  // SVG-Stempel mit "Bezahlt" anzeigen
}
```

### Gastansicht

- Logo-Skalierung per `custom.css`
- Zahlungsbutton nur bei offenen Beträgen sichtbar

---

## 🚀 Deployment mit Nova

### Empfohlener Task (Beispiel)

```bash
rsync -avz ./application/views/invoice_templates/ \
      user@webhost:/pfad/invoiceplane/application/views/invoice_templates/

rsync ./assets/core/css/custom.css \
      user@webhost:/pfad/invoiceplane/assets/core/css/custom.css
```

---

## 📌 Nächstes To-do

- Import alter Zahlungen
- Abgleich Rechnungshistorie
- Prüfung von Mahnstufen/Status
- Optional: Automatisierter Monatsabschluss mit Entwurf-Rechnung

---

## ✍️ Autor

Patrick „George“  
https://davidt.de