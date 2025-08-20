export interface Role {
  id: number;
  name: string;
  description?: string;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
  role_id: number;
  status: 'active' | 'inactive';
  created_at: string;
  updated_at: string;
  role?: Role;
}

export interface CreditCard {
  id: number;
  user_id: number;
  token: string;
  last4: string;
  brand: 'visa' | 'mastercard' | 'amex' | 'diners' | 'discover' | 'jcb' | 'other';
  exp_month: number;
  exp_year: number;
  holder_name?: string;
  is_default: boolean;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  role_id: number;
}

export interface ApiResponse<T = any> {
  data: T;
  message?: string;
  status: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}