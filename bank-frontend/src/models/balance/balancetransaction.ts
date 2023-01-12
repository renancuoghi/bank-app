import { IEntity } from '../shared/baseinterfaces';
import { IUser } from '../user/userinterface';

export enum TransactionType {
  CREDIT = 'C',
  DEBIT = 'D',
  ALL = 'ALL',
}

export enum TransactionStatus {
  PENDING = 'P',
  ACCEPTED = 'A',
  REJECTED = 'R',
}
export interface ITransactionBase {
  amount: number;
  description: string;
  transaction_type: TransactionType;
}

export interface ITransactionInput extends ITransactionBase {
  image: unknown;
  created_at: string | null;
}

export interface ITransaction extends ITransactionBase, IEntity {
  user_id: number;
  balance_id: number;
  status: TransactionStatus;
  path_image: string;
  approved_user_id: number;
}

export interface ITransactionFull extends ITransaction {
  user: IUser;
}

export interface IBalance extends IEntity {
  total: number;
  user_id: number;
}

export interface ITotalTransactionType {
  incoming: number;
  expenses: number;
}
