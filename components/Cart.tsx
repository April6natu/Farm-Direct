
import React, { useState } from 'react';
import { CartItem, Product } from '../types';
import { SUPPORTED_AREAS } from '../constants';

interface CartProps {
  isOpen: boolean;
  onClose: () => void;
  items: CartItem[];
  onRemove: (productId: string) => void;
  onCheckout: (location: string, paymentMethod: 'MOBILE_MONEY' | 'CREDIT_CARD') => void;
}

const Cart: React.FC<CartProps> = ({ isOpen, onClose, items, onRemove, onCheckout }) => {
  const [step, setStep] = useState<'ITEMS' | 'CHECKOUT'>('ITEMS');
  const [location, setLocation] = useState(SUPPORTED_AREAS[0]);
  const [paymentMethod, setPaymentMethod] = useState<'MOBILE_MONEY' | 'CREDIT_CARD'>('MOBILE_MONEY');

  const total = items.reduce((acc, item) => acc + (item.price * item.quantity), 0);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] overflow-hidden">
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={onClose} />
      <div className="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl flex flex-col animate-in slide-in-from-right duration-300">
        <div className="p-6 border-b flex justify-between items-center bg-emerald-700 text-white">
          <h2 className="text-xl font-bold">Your Produce Basket</h2>
          <button onClick={onClose} className="p-2 hover:bg-emerald-600 rounded-full transition-colors">
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div className="flex-grow overflow-y-auto p-6">
          {items.length === 0 ? (
            <div className="h-full flex flex-col items-center justify-center text-gray-400 space-y-4">
              <svg className="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <p className="font-medium">Your basket is empty.</p>
              <button 
                onClick={onClose}
                className="text-emerald-600 font-bold hover:underline"
              >
                Start Shopping
              </button>
            </div>
          ) : step === 'ITEMS' ? (
            <div className="space-y-6">
              {items.map(item => (
                <div key={item.id} className="flex items-center space-x-4">
                  <img src={item.image} alt={item.name} className="w-16 h-16 rounded-xl object-cover bg-gray-100" />
                  <div className="flex-grow">
                    <h4 className="font-bold text-gray-900 text-sm">{item.name}</h4>
                    <p className="text-xs text-gray-500">{item.quantity} x ${item.price.toFixed(2)} / {item.unit}</p>
                  </div>
                  <button 
                    onClick={() => onRemove(item.id)}
                    className="text-red-400 hover:text-red-600 p-2"
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              ))}
            </div>
          ) : (
            <div className="space-y-8">
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">Delivery Location</label>
                <select 
                  value={location}
                  onChange={(e) => setLocation(e.target.value)}
                  className="w-full bg-gray-50 border rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500"
                >
                  {SUPPORTED_AREAS.map(area => <option key={area} value={area}>{area}</option>)}
                </select>
                <p className="text-[10px] text-gray-400 mt-1 italic">Deliveries are made within 24 hours.</p>
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">Payment Method</label>
                <div className="grid grid-cols-2 gap-4">
                  <button 
                    onClick={() => setPaymentMethod('MOBILE_MONEY')}
                    className={`p-4 rounded-xl border-2 flex flex-col items-center justify-center space-y-2 transition-all ${
                      paymentMethod === 'MOBILE_MONEY' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 hover:border-gray-200'
                    }`}
                  >
                    <svg className="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span className="text-xs font-bold">Mobile Money</span>
                  </button>
                  <button 
                    onClick={() => setPaymentMethod('CREDIT_CARD')}
                    className={`p-4 rounded-xl border-2 flex flex-col items-center justify-center space-y-2 transition-all ${
                      paymentMethod === 'CREDIT_CARD' ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 hover:border-gray-200'
                    }`}
                  >
                    <svg className="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span className="text-xs font-bold">Credit Card</span>
                  </button>
                </div>
              </div>
            </div>
          )}
        </div>

        {items.length > 0 && (
          <div className="p-6 border-t bg-gray-50 space-y-4">
            <div className="flex justify-between items-center font-bold text-gray-900 text-lg">
              <span>Total Amount</span>
              <span>${total.toFixed(2)}</span>
            </div>
            
            {step === 'ITEMS' ? (
              <button 
                onClick={() => setStep('CHECKOUT')}
                className="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform active:scale-[0.98]"
              >
                Proceed to Checkout
              </button>
            ) : (
              <div className="flex space-x-3">
                <button 
                  onClick={() => setStep('ITEMS')}
                  className="flex-1 bg-white border border-gray-200 text-gray-600 font-bold py-4 rounded-2xl hover:bg-gray-100 transition-colors"
                >
                  Back
                </button>
                <button 
                  onClick={() => onCheckout(location, paymentMethod)}
                  className="flex-[2] bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-transform active:scale-[0.98]"
                >
                  Confirm Purchase
                </button>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default Cart;
