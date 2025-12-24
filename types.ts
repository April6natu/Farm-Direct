
export enum UserRole {
  ADMIN = 'ADMIN',
  SELLER = 'SELLER',
  BUYER = 'BUYER'
}

export interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  avatar?: string;
}

export interface Product {
  id: string;
  sellerId: string;
  name: string;
  category: string;
  price: number;
  unit: string;
  description: string;
  image: string;
  stock: number;
}

export interface CartItem extends Product {
  quantity: number;
}

export interface Order {
  id: string;
  buyerId: string;
  items: CartItem[];
  total: number;
  status: 'PENDING' | 'PAID' | 'DELIVERED';
  deliveryLocation: string;
  createdAt: string;
}

export interface Notification {
  id: string;
  userId: string;
  message: string;
  read: boolean;
  createdAt: string;
}
