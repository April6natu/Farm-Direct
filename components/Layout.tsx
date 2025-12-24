
import React from 'react';
import { UserRole, User } from '../types';

interface LayoutProps {
  children: React.ReactNode;
  user: User | null;
  onLogout: () => void;
  onOpenCart: () => void;
  cartCount: number;
}

const Layout: React.FC<LayoutProps> = ({ children, user, onLogout, onOpenCart, cartCount }) => {
  return (
    <div className="min-h-screen flex flex-col">
      <header className="bg-emerald-700 text-white shadow-lg sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <div className="bg-white p-1 rounded-lg">
              <svg className="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </div>
            <h1 className="text-xl font-bold tracking-tight">Farm-Direct</h1>
          </div>

          <div className="flex items-center space-x-4">
            {user ? (
              <>
                {user.role === UserRole.BUYER && (
                  <button 
                    onClick={onOpenCart}
                    className="relative p-2 hover:bg-emerald-600 rounded-full transition-colors"
                  >
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {cartCount > 0 && (
                      <span className="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {cartCount}
                      </span>
                    )}
                  </button>
                )}
                <div className="hidden md:flex flex-col items-end mr-2">
                  <span className="text-sm font-medium">{user.name}</span>
                  <span className="text-[10px] uppercase opacity-75">{user.role}</span>
                </div>
                <button 
                  onClick={onLogout}
                  className="bg-emerald-800 hover:bg-emerald-900 px-4 py-2 rounded-lg text-sm transition-colors"
                >
                  Logout
                </button>
              </>
            ) : (
              <span className="text-sm">Welcome to the Market</span>
            )}
          </div>
        </div>
      </header>

      <main className="flex-grow max-w-7xl mx-auto w-full px-4 py-8">
        {children}
      </main>

      <footer className="bg-white border-t py-8">
        <div className="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
          &copy; {new Date().getFullYear()} Farm-Direct. Connecting farmers to your table.
        </div>
      </footer>
    </div>
  );
};

export default Layout;
