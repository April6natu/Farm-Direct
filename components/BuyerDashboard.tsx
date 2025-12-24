
import React, { useState } from 'react';
import { Product } from '../types';
import { CATEGORIES } from '../constants';

interface BuyerDashboardProps {
  products: Product[];
  onAddToCart: (product: Product) => void;
}

const BuyerDashboard: React.FC<BuyerDashboardProps> = ({ products, onAddToCart }) => {
  const [selectedCategory, setSelectedCategory] = useState('All');

  const filteredProducts = selectedCategory === 'All' 
    ? products 
    : products.filter(p => p.category === selectedCategory);

  return (
    <div className="space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h2 className="text-2xl font-bold text-gray-800">Fresh from the Farm</h2>
          <p className="text-gray-500">Find quality agricultural products at the best prices.</p>
        </div>
        <div className="flex flex-wrap gap-2">
          {CATEGORIES.map(cat => (
            <button
              key={cat}
              onClick={() => setSelectedCategory(cat)}
              className={`px-4 py-2 rounded-full text-sm font-medium transition-all ${
                selectedCategory === cat 
                ? 'bg-emerald-600 text-white shadow-md' 
                : 'bg-white text-gray-600 hover:bg-gray-100'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {filteredProducts.map(product => (
          <div key={product.id} className="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow group">
            <div className="relative h-48 overflow-hidden">
              <img 
                src={product.image} 
                alt={product.name} 
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-emerald-700">
                {product.category}
              </div>
            </div>
            <div className="p-4 space-y-3">
              <div className="flex justify-between items-start">
                <h3 className="font-bold text-gray-900 line-clamp-1">{product.name}</h3>
                <span className="text-emerald-600 font-bold">${product.price.toFixed(2)}</span>
              </div>
              <p className="text-sm text-gray-500 line-clamp-2 min-h-[40px]">
                {product.description}
              </p>
              <div className="flex items-center justify-between pt-2">
                <span className="text-xs text-gray-400">per {product.unit}</span>
                <button
                  onClick={() => onAddToCart(product)}
                  className="bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center space-x-1"
                >
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  <span>Add to Cart</span>
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {filteredProducts.length === 0 && (
        <div className="text-center py-20 bg-gray-50 rounded-2xl border-2 border-dashed">
          <p className="text-gray-400">No products found in this category.</p>
        </div>
      )}
    </div>
  );
};

export default BuyerDashboard;
