# xlsx-survey — research scripts from Session 1 (2026-04-22)

Two short Python scripts used during Sprint 00 Session 1 to inspect the three spreadsheets in `C:\xampp\htdocs\ltat-fitness-module\helpful-project-documents\` (the two BPP 5-Day workbooks and the training-load xlsm).

- **`survey_xlsx.py`** — lists sheet names in each workbook. Quick reconnaissance.
- **`dump_sheets.py`** — dumps rows 1-N / cols 1-M of chosen sheets to stdout, one cell per column separated by `|`. Used to read the Start Here Guide, Assessment, Big Picture, How to Guide, Control Panel, and Training Load Data sheets.

Run via:
```bash
python survey_xlsx.py
python dump_sheets.py | head -400
```

Requires Python 3.x + `openpyxl` installed (`pip install openpyxl`).

**Status:** reference only. Results were captured and analysed in `.ai/.ai2/ltat-fitness-findings.md` (§9) and `.ai/.ai2/HARD_LESSONS.md` (HL-9). The scripts are kept so any future agent who needs to re-inspect the workbooks can do so without rewriting this wiring.

Safe to delete if the workbooks themselves are no longer on disk.
