export type SuitCode = 'C' | 'D' | 'S' | 'H';

const suitSymbols: Record<SuitCode, string> = {
    C: '♣',
    D: '♦',
    S: '♠',
    H: '♥',
};

const suitNames: Record<SuitCode, string> = {
    C: 'clubs',
    D: 'diamonds',
    S: 'spades',
    H: 'hearts',
};

const rankNames: Record<string, string> = {
    T: '10',
    J: 'J',
    Q: 'Q',
    K: 'K',
    A: 'A',
};

const rankWords: Record<string, string> = {
    T: 'Ten',
    J: 'Jack',
    Q: 'Queen',
    K: 'King',
    A: 'Ace',
};

export function cardSuit(code: string): SuitCode {
    return code.charAt(1) as SuitCode;
}

export function cardRankLabel(code: string): string {
    const rank = code.charAt(0);

    return rankNames[rank] ?? rank;
}

export function suitSymbol(code: string): string {
    return suitSymbols[cardSuit(code)];
}

export function isRedCard(code: string): boolean {
    const suit = cardSuit(code);

    return suit === 'H' || suit === 'D';
}

export function cardName(code: string): string {
    const rank = code.charAt(0);

    return `${rankWords[rank] ?? rank} of ${suitNames[cardSuit(code)]}`;
}

/** Seat positions run clockwise, so the next position is to a Player's left. */
export function sideOf(position: number, myPosition: number) {
    const sides = ['bottom', 'left', 'top', 'right'] as const;

    return sides[(position - myPosition + 4) % 4];
}
