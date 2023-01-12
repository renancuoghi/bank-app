export interface IResponse<T> {
  status: boolean;
  data?: T | null;
}

export interface IResponseSuccess<T> extends IResponse<T> {
  message?: string;
}

export interface IResponseError<T> extends IResponse<T> {
  error?: string;
}

export interface IEntity {
  id: number;
  created_at: Date;
  updated_at: Date;
}

export interface IPaginator<T> {
  total: number;
  page_size: number;
  total_pages: number;
  items: T[];
}

export type IPaginationResponse<T> = IResponse<IPaginator<T>>;

export interface IOption {
  option: string;
  label: string;
}
