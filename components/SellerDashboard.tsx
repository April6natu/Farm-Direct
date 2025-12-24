
import React, { useState, useEffect } from 'react';
import { Product, User, Notification } from '../types';
import { generateProductDescription, getMarketAdvice } from '../services/geminiService';

interface SellerDashboardProps {
  user: User;
  products: Product[];
  onAddProduct: (product: Omit<Product, 'id' | 'sellerId'>) => void;
  onUpdateProduct: (id: string, data: Partial<Product>) => void;
  onDeleteProduct: (id: string) => void;
  notifications: Notification[];
}

const SellerDashboard: React.FC<SellerDashboardProps> = ({ user, products, onAddProduct, onUpdateProduct, onDeleteProduct, notifications }) => {
  const [modalMode, setModalMode] = useState<'ADD' | 'EDIT' | null>(null);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [formData, setFormData] = useState({ name: '', category: 'Tubers', price: 0, unit: 'kg', description: '', stock: 10, image: 'https://picsum.photos/400/300' });
  const [isGenerating, setIsGenerating] = useState(false);
  const [marketTip, setMarketTip] = useState('');

  const myProducts = products.filter(p => p.sellerId === user.id);

  useEffect(() => {
    if (myProducts.length > 0) {
      getMarketAdvice(myProducts[0].name).then(setMarketTip);
    }
  }, [myProducts.length]);

  const openEdit = (product: Product) => {
    setEditingId(product.id);
    setFormData({ ...product });
    setModalMode('EDIT');
  };

  const handleAutoDescription = async () => {
    if (!formData.name) return;
    setIsGenerating(true);
    const desc = await generateProductDescription(formData.name, formData.category);
    setFormData(prev => ({ ...prev, description: desc || '' }));
    setIsGenerating(false);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modalMode === 'ADD') {
      onAddProduct(formData);
    } else if (modalMode === 'EDIT' && editingId) {
      onUpdateProduct(editingId, formData);
    }
    setModalMode(null);
    setFormData({ name: '', category: 'Tubers', price: 0, unit: 'kg', description: '', stock: 10, image: 'https://picsum.photos/400/300' });
  };

  return (
    <div className="space-y-8">
      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold">Seller Hub</h2>
        <button onClick={() => setModalMode('ADD')} className="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold">Add Product</button>
      </div>

      <div className="bg-white rounded-2xl border p-6">
        <h3 className="font-bold mb-4">Inventory ({myProducts.length})</h3>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="border-b">
              <tr>
                <th className="pb-4">Name</th>
                <th className="pb-4">Price</th>
                <th className="pb-4">Stock</th>
                <th className="pb-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {myProducts.map(p => (
                <tr key={p.id}>
                  <td className="py-4 font-medium">{p.name}</td>
                  <td className="py-4 text-emerald-600 font-bold">${p.price}/{p.unit}</td>
                  <td className="py-4">{p.stock}</td>
                  <td className="py-4 text-right space-x-2">
                    <button onClick={() => openEdit(p)} className="text-blue-600 hover:underline">Edit</button>
                    <button onClick={() => onDeleteProduct(p.id)} className="text-red-600 hover:underline">Delete</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {modalMode && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl w-full max-w-xl p-8">
            <h3 className="text-xl font-bold mb-6">{modalMode === 'ADD' ? 'Add New' : 'Edit'} Product</h3>
            <form onSubmit={handleSubmit} className="space-y-4">
              <input type="text" placeholder="Name" required value={formData.name} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full border rounded-xl px-4 py-2" />
              <div className="grid grid-cols-2 gap-4">
                <input type="number" placeholder="Price" required value={formData.price} onChange={e => setFormData({...formData, price: +e.target.value})} className="border rounded-xl px-4 py-2" />
                <input type="number" placeholder="Stock" required value={formData.stock} onChange={e => setFormData({...formData, stock: +e.target.value})} className="border rounded-xl px-4 py-2" />
              </div>
              <div className="flex justify-between text-xs">
                <span className="font-bold text-gray-500">Description</span>
                <button type="button" onClick={handleAutoDescription} className="text-emerald-600 font-bold underline">{isGenerating ? '...' : '✨ Use AI'}</button>
              </div>
              <textarea rows={3} value={formData.description} onChange={e => setFormData({...formData, description: e.target.value})} className="w-full border rounded-xl px-4 py-2" />
              <div className="flex space-x-3 pt-4">
                <button type="button" onClick={() => setModalMode(null)} className="flex-1 border py-2 rounded-xl">Cancel</button>
                <button type="submit" className="flex-1 bg-emerald-600 text-white py-2 rounded-xl">Save</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default SellerDashboard;
