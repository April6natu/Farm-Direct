
import React, { useState } from 'react';
import { User, UserRole, Product, Order, CartItem, Notification } from './types';
import { INITIAL_PRODUCTS } from './constants';
import Layout from './components/Layout';
import BuyerDashboard from './components/BuyerDashboard';
import SellerDashboard from './components/SellerDashboard';
import AdminDashboard from './components/AdminDashboard';
import Cart from './components/Cart';

const App: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);
  const [products, setProducts] = useState<Product[]>(INITIAL_PRODUCTS);
  const [cart, setCart] = useState<CartItem[]>([]);
  const [orders, setOrders] = useState<Order[]>([]);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [view, setView] = useState<'AUTH' | 'DASHBOARD'>('AUTH');

  const [users, setUsers] = useState<User[]>([
    { id: 'admin_1', name: 'System Admin', email: 'admin@farm-direct.com', role: UserRole.ADMIN },
    { id: 'seller_1', name: 'John Farmer', email: 'john@farm-direct.com', role: UserRole.SELLER },
    { id: 'buyer_1', name: 'Mary Green', email: 'mary@test.com', role: UserRole.BUYER }
  ]);

  // --- USER CRUD ---
  const handleAddUser = (userData: Omit<User, 'id'>) => {
    const newUser = { ...userData, id: Math.random().toString(36).substr(2, 9) };
    setUsers(prev => [...prev, newUser]);
  };

  const handleUpdateUser = (id: string, data: Partial<User>) => {
    setUsers(prev => prev.map(u => u.id === id ? { ...u, ...data } : u));
    if (user?.id === id) setUser(prev => prev ? { ...prev, ...data } : null);
  };

  const handleDeleteUser = (id: string) => {
    if (confirm('Are you sure you want to remove this user?')) {
      setUsers(prev => prev.filter(u => u.id !== id));
      if (user?.id === id) handleLogout();
    }
  };

  // --- PRODUCT CRUD ---
  const handleAddProduct = (productData: Omit<Product, 'id' | 'sellerId'>) => {
    if (!user) return;
    const newProduct: Product = {
      ...productData,
      id: Math.random().toString(36).substr(2, 9),
      sellerId: user.id
    };
    setProducts(prev => [newProduct, ...prev]);
  };

  const handleUpdateProduct = (id: string, data: Partial<Product>) => {
    setProducts(prev => prev.map(p => p.id === id ? { ...p, ...data } : p));
  };

  const handleDeleteProduct = (id: string) => {
    if (confirm('Are you sure you want to delete this product listing?')) {
      setProducts(prev => prev.filter(p => p.id !== id));
      setCart(prev => prev.filter(item => item.id !== id));
    }
  };

  // --- ORDER CRUD ---
  const handleUpdateOrderStatus = (orderId: string, status: Order['status']) => {
    setOrders(prev => prev.map(o => o.id === orderId ? { ...o, status } : o));
  };

  // --- AUTH ---
  const handleLogin = (role: UserRole) => {
    const foundUser = users.find(u => u.role === role);
    if (foundUser) {
      setUser(foundUser);
      setView('DASHBOARD');
    }
  };

  const handleLogout = () => {
    setUser(null);
    setView('AUTH');
    setCart([]);
  };

  // --- BUYER ACTIONS ---
  const handleAddToCart = (product: Product) => {
    setCart(prev => {
      const existing = prev.find(item => item.id === product.id);
      if (existing) {
        return prev.map(item => item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item);
      }
      return [...prev, { ...product, quantity: 1 }];
    });
    setIsCartOpen(true);
  };

  const handleRemoveFromCart = (productId: string) => {
    setCart(prev => prev.filter(item => item.id !== productId));
  };

  const handleCheckout = (location: string, paymentMethod: string) => {
    if (!user) return;
    const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const newOrder: Order = {
      id: Math.random().toString(36).substr(2, 9),
      buyerId: user.id,
      items: [...cart],
      total,
      status: 'PENDING',
      deliveryLocation: location,
      createdAt: new Date().toISOString()
    };
    setOrders(prev => [...prev, newOrder]);
    cart.forEach(item => {
      setNotifications(prev => [{
        id: Math.random().toString(36).substr(2, 9),
        userId: item.sellerId,
        message: `Sale! ${item.quantity} ${item.unit} of ${item.name} purchased.`,
        read: false,
        createdAt: new Date().toISOString()
      }, ...prev]);
      // Update stock (Read-Update pattern)
      handleUpdateProduct(item.id, { stock: Math.max(0, item.stock - item.quantity) });
    });
    setCart([]);
    setIsCartOpen(false);
    alert(`Success! Delivery to ${location} scheduled.`);
  };

  return (
    <>
      <Layout 
        user={user} 
        onLogout={handleLogout} 
        onOpenCart={() => setIsCartOpen(true)}
        cartCount={cart.reduce((acc, item) => acc + item.quantity, 0)}
      >
        {view === 'AUTH' ? (
          <div className="max-w-md mx-auto mt-20 p-8 bg-white rounded-3xl shadow-xl border border-gray-100 text-center">
            <h2 className="text-2xl font-bold mb-6">Select a Role to Demo</h2>
            <div className="space-y-3">
              {[UserRole.BUYER, UserRole.SELLER, UserRole.ADMIN].map(role => (
                <button key={role} onClick={() => handleLogin(role)} className="w-full p-4 bg-gray-50 border rounded-2xl hover:bg-emerald-50 font-bold transition-all">
                  {role} Login
                </button>
              ))}
            </div>
          </div>
        ) : (
          <div>
            {user?.role === UserRole.BUYER && (
              <BuyerDashboard products={products} onAddToCart={handleAddToCart} />
            )}
            {user?.role === UserRole.SELLER && (
              <SellerDashboard 
                user={user} 
                products={products} 
                onAddProduct={handleAddProduct} 
                onUpdateProduct={handleUpdateProduct}
                onDeleteProduct={handleDeleteProduct}
                notifications={notifications.filter(n => n.userId === user.id)}
              />
            )}
            {user?.role === UserRole.ADMIN && (
              <AdminDashboard 
                users={users} 
                products={products} 
                orders={orders}
                onAddUser={handleAddUser}
                onUpdateUser={handleUpdateUser}
                onDeleteUser={handleDeleteUser}
                onUpdateOrderStatus={handleUpdateOrderStatus}
              />
            )}
          </div>
        )}
      </Layout>
      <Cart 
        isOpen={isCartOpen} 
        onClose={() => setIsCartOpen(false)} 
        items={cart} 
        onRemove={handleRemoveFromCart}
        onCheckout={handleCheckout}
      />
    </>
  );
};

export default App;
