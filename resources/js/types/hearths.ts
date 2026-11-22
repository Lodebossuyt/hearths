export type RoomStatus = 'waiting' | 'playing' | 'finished' | 'abandoned';
export type RoundStatus = 'passing' | 'playing' | 'finished';
export type PassDirection = 'left' | 'right' | 'across' | 'none';

export type SeatView = {
    position: number;
    name: string;
    userId: number;
    score: number;
    cardCount: number;
    hasPassed: boolean;
    isTurn: boolean;
    isMe: boolean;
};

export type HandCard = {
    code: string;
    playable: boolean;
    received: boolean;
};

export type TrickView = {
    number: number;
    plays: { position: number; card: string }[];
    winnerPosition: number | null;
};

export type RoundView = {
    number: number;
    passDirection: PassDirection;
    status: RoundStatus;
    heartsBroken: boolean;
    myPoints: number;
    hand: HandCard[];
    hasPassed: boolean;
    passRecipientPosition: number | null;
    turnPosition: number | null;
    trick: TrickView | null;
};

export type HistoryEntry = {
    number: number;
    scores: Record<number, number>;
    moonShooter: number | null;
};

export type TableView = {
    code: string;
    status: RoomStatus;
    myPosition: number | null;
    seats: SeatView[];
    round: RoundView | null;
    history: HistoryEntry[];
    winners: number[];
    abandonedBy: string | null;
};

export type LobbyRoom = {
    code: string;
    players: string[];
    freeSeats: number;
};

/** Where a Seat sits on screen relative to the viewing Player. */
export type TableSide = 'bottom' | 'left' | 'top' | 'right';
