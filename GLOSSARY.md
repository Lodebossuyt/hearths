# Hearths

A browser-based, 4-player online version of the traditional card game Hearts.

## People

**Player**:
A registered person, known to others by their display name.
_Avoid_: User (outside auth code), account, member

## Rooms

**Room**:
A virtual table with four Seats where Players gather to play a Game. Can be joined from the lobby or through its invite link.
_Avoid_: Table, lobby, match, session

**Seat**:
One of the four positions in a Room, occupied by at most one Player. Seat order determines who is left, right and across.
_Avoid_: Slot, position, spot

**Away**:
The status of a seated Player who currently has no browser connected to the Room. The Game waits for them.
_Avoid_: Disconnected, offline, idle

**Lobby**:
The page listing Rooms that are still waiting for Players.
_Avoid_: Room list, matchmaking

## Play

**Game**:
A complete contest played over as many Rounds as it takes until a Player reaches 100 points. The lowest score wins.
_Avoid_: Match

**Round**:
One deal of the cards, consisting of a Pass followed by 13 Tricks.
_Avoid_: Hand (when meaning the deal), deal

**Hand**:
The cards a single Player currently holds. It is private to that Player.
_Avoid_: Cards (when meaning the whole set held)

**Pass**:
The exchange at the start of a Round where every Player gives three cards to another Player (left, right, across, or no pass, in rotation).
_Avoid_: Swap, trade

**Trick**:
One card from each Player, starting with the Lead. Taken by the highest card of the led suit.
_Avoid_: Turn

**Lead**:
The first card played to a Trick, which sets the suit others must follow.

**Hearts broken**:
The state in a Round once a heart has been played, after which hearts may be led.

**Shooting the moon**:
Taking all hearts and the Q♠ in a single Round. Every other Player then scores 26 instead.

## Scoring

**Points**:
The penalty a Player has taken in the current Round (1 per heart, 13 for the Q♠). Only the Player who took them can see them until the Round ends.
_Avoid_: Round score

**Score**:
A Player's total Points across all finished Rounds of the Game. Every Player can see it.
_Avoid_: Total, points (when meaning the total)

**Winner**:
The Player with the lowest Score when the Game finishes. If several share the lowest Score, they are all Winners (co-winners).

## Outcomes

**Abandoned**:
A Game ended early because a seated Player gave up. It has no winner.
_Avoid_: Forfeited, cancelled
