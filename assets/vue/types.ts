export interface Member {
    id: number,
    email: string,
    invitationAccepted: boolean,
}

export interface SecretSanta {
    id: number,
    label: string,
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

interface DataValue {
    type: 'MONEY' | 'GIFT' | 'EVENT',
    id: number,
    data: Money|Gift|Event,
}

export interface TaskResponse {
    id: number
    state: State,
    data: DataValue,
}

export interface Item {
    id: number,
    state: 'PENDING' | 'SUCCESS' | 'ERROR',
    data: {
        type: WishType
    }
}