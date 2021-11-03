# MAO-Management

Installation Instruction to Come!

## To-do

- FAMAT ID Updater
- Change `start_session();` to `safeStartSession();` where possible.
- Remove `time_last_login` from `login` table.
- Add remove checkoff buttons for Competition Selections
- Competition Tracker:
  - Add "Deselect" button.
  - Competition Tracker Deselect Page Should Have Statistics on all Comps.

  - _For a particular competition_:
    - Display the # of **registered** participants
    - Should list the number of participants who have not been added next to the "Selections" button
  - **_Accounting!_:**
    - Each transaction should have an amount owed and paid.
    - Totals (and Grand Total) are calculated from transactions and payment information on-the-spot.
    - Update button.
    - History log (**very important!**).
    - Individual transactions can only be deleted with confirmation and transactions **MUST** be archived in a separate
      table with **ALL** information relating to the transaction **and** payment (you've got to be able to know
      everything about when the payment is for, transaction history, etc.). Hard copies **MUST** still be kept!
- Administration Panel (_TBD, WIP_)
- Installation Instructions
