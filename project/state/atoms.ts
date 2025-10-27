import { atom } from 'recoil';
import { Person } from '../types/person';

export const likedPeopleState = atom<Person[]>({
  key: 'likedPeopleState',
  default: [],
});

export const dislikedPeopleState = atom<string[]>({
  key: 'dislikedPeopleState',
  default: [],
});

export const currentIndexState = atom<number>({
  key: 'currentIndexState',
  default: 0,
});
