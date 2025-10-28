import { atom } from 'recoil';

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface AuthState {
  token: string | null;
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
}

export const authState = atom<AuthState>({
  key: 'authState',
  default: {
    token: null,
    user: null,
    isAuthenticated: false,
    isLoading: false,
    error: null,
  },
});
