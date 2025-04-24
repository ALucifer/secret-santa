export interface Member {
    id: number,
    email: string,
    invitationAccepted: boolean,
}

export enum WishItemFormType {
    GIFT = 'GIFT',
    MONEY= 'MONEY',
    EVENT = 'EVENT',
}

export interface WishItemForm {
    type: WishItemFormType,
    data: any
}