*********************.
***  Import data  ***.
*********************.

* NB: vergeet niet om de drive en folder waarin de datafile
* staat opgeslagen, toe te voegen in /FILE="[GJYP_FILENAME]".

GET DATA
  /TYPE=TXT
  /FILE="[GJYP_FILENAME]"
  /DELCASE=LINE
  /DELIMITERS=","
  /ARRANGEMENT=DELIMITED
  /FIRSTCASE=1
  /IMPORTCASE=ALL
  /VARIABLES=
  id F3.0
  type F1.0
  cond F2.0
  cond_sc F1.0
  cond_rv F1.0
  v1 F1.0
  v2 F1.0
  v3 F1.0
  v4 F1.0
  sekse F1.0
  leeftijd F2.0
  v7 F1.0
  v8 F1.0
  v9 F1.0
  v10 F1.0
  v11 F1.0.
CACHE.
EXECUTE.
DATASET NAME PsychologischExperiment WINDOW=FRONT.

**************************.
***  Set value labels  ***.
**************************.

VALUE LABELS cond_sc
  1 'mild'
  2 'streng'.
VALUE LABELS cond_rv
  1 'rechtvaardig'
  2 'onrechtvaardig'.
VALUE LABELS v1 v2 v3 v9 v10 v11
  1 'nee, nooit'
  7 'ja, heel vaak'.
VALUE LABELS v7
  1 'zeer soepel'
  7 'zeer streng'.
VALUE LABELS v8
  1 'heel oneerlijk'
  7 'heel eerlijk'.
VALUE LABELS sekse
  1 'man'
  2 'vrouw'.

EXECUTE.