/************************   ENTITY  ************************************/
export interface Member {
    id: number,
    email: string,
    invitationAccepted: boolean,
    state: string,
}

export interface SecretSanta {
    id: number,
    label: string,
}

export interface WishItem {
    id: number,
    type: keyof typeof WishItemType,
    data: Money | Gift | Event,
}

/************************   ENTITY  ************************************/
/************************   Wish item  ************************************/

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

export enum WishItemType {
    GIFT = 'GIFT',
    MONEY= 'MONEY',
    EVENT = 'EVENT',
}

export interface WishItemForm {
    type: WishItemType,
    data: Money | Gift | Event
}

type TaskResponseState = 'PENDING' | 'SUCCESS' | 'ERROR'
type ItemState = 'PENDING' | 'SUCCESS' | 'ERROR'

interface DataValue {
    type: 'MONEY' | 'GIFT' | 'EVENT',
    id: number,
    data: Money|Gift|Event,
}

export interface TaskResponse {
    id: number
    state: TaskResponseState,
    data: DataValue,
}

export interface Item {
    id: number,
    state: ItemState,
    data: {
        type: keyof typeof WishItemType,
    }
}