import React, { createContext, useContext, useState, ReactNode } from 'react';
import { Person } from '../types/person';

interface LikedPeopleContextType {
  likedPeople: Person[];
  addLikedPerson: (person: Person) => void;
  removeLikedPerson: (personId: string) => void;
}

const LikedPeopleContext = createContext<LikedPeopleContextType | undefined>(undefined);

export function LikedPeopleProvider({ children }: { children: ReactNode }) {
  const [likedPeople, setLikedPeople] = useState<Person[]>([]);

  const addLikedPerson = (person: Person) => {
    setLikedPeople((prev) => [...prev, person]);
  };

  const removeLikedPerson = (personId: string) => {
    setLikedPeople((prev) => prev.filter((p) => p.id !== personId));
  };

  return (
    <LikedPeopleContext.Provider value={{ likedPeople, addLikedPerson, removeLikedPerson }}>
      {children}
    </LikedPeopleContext.Provider>
  );
}

export function useLikedPeople() {
  const context = useContext(LikedPeopleContext);
  if (!context) {
    throw new Error('useLikedPeople must be used within LikedPeopleProvider');
  }
  return context;
}
