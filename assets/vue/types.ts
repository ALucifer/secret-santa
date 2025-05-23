export interface Member {
    id: number,
    email: string,
    invitationAccepted: boolean,
}

interface Money {
    price: number
}
interface Gift {
    url: string
}
interface Event {
    date: Date,
    name: string
}

export enum WishType {
    GIFT = 'GIFT',
    MONEY= 'MONEY',
    EVENT = 'EVENT',
}

export interface WishItemForm {
    type: WishType,
    data: Money | Gift | Event
}

export enum State {
    PENDING = 'PENDING',
    SUCCESS = 'SUCCESS',
    ERROR = 'ERROR',
}

export interface TaskResponse {
    id: number
    state: State,
    data: Array<any>,
}