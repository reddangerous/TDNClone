export interface Person {
  id: string;
  name: string;
  age: number;
  pictures: string[];
  location: string;
}

export interface SwipeAction {
  personId: string;
  action: 'like' | 'dislike';
}
