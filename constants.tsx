
import { Product, UserRole } from './types';

export const SUPPORTED_AREAS = [
  'Central Business District',
  'North Industrial Park',
  'Riverside Estates',
  'Lakeside Heights',
  'Market Square Area',
  'Green Valley Farm Zone'
];

export const INITIAL_PRODUCTS: Product[] = [
  {
    id: '1',
    sellerId: 'seller_1',
    name: 'Fresh Cassava Tubers',
    category: 'Tubers',
    price: 15.50,
    unit: 'kg',
    description: 'High-quality white cassava tubers, freshly harvested from the fertile soils of the North.',
    image: 'https://picsum.photos/seed/cassava/400/300',
    stock: 500
  },
  {
    id: '2',
    sellerId: 'seller_1',
    name: 'Yellow Plantains',
    category: 'Fruits/Staples',
    price: 12.00,
    unit: 'Bunch',
    description: 'Sweet and ripe yellow plantains, perfect for frying or boiling.',
    image: 'https://picsum.photos/seed/plantain/400/300',
    stock: 120
  },
  {
    id: '3',
    sellerId: 'seller_2',
    name: 'Long Grain White Rice',
    category: 'Grains',
    price: 45.00,
    unit: '25kg Bag',
    description: 'Premium long grain white rice, stone-free and easy to cook.',
    image: 'https://picsum.photos/seed/rice/400/300',
    stock: 45
  },
  {
    id: '4',
    sellerId: 'seller_2',
    name: 'Brown Beans (Honey Beans)',
    category: 'Legumes',
    price: 8.50,
    unit: 'kg',
    description: 'Rich and nutritious brown beans, sourced directly from smallholder farmers.',
    image: 'https://picsum.photos/seed/beans/400/300',
    stock: 200
  },
  {
    id: '5',
    sellerId: 'seller_3',
    name: 'Fresh Red Tomatoes',
    category: 'Vegetables',
    price: 5.00,
    unit: 'Basket',
    description: 'Plump and juicy vine-ripened red tomatoes.',
    image: 'https://picsum.photos/seed/tomato/400/300',
    stock: 80
  }
];

export const CATEGORIES = ['All', 'Tubers', 'Grains', 'Legumes', 'Vegetables', 'Fruits/Staples'];
