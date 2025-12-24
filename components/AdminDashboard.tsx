
import React, { useState } from 'react';
import { User, Product, Order, UserRole } from '../types';

interface AdminDashboardProps {
  users: User[];
  products: Product[];
  orders: Order[];
  onAddUser: (u: Omit<User, 'id'>) => void;
  onUpdateUser: (id: string, d: Partial<User>) => void;
  onDeleteUser: (id: string) => void;
  onUpdateOrderStatus: (id: string, s: Order['status']) => void;
}

const AdminDashboard: React.FC<AdminDashboardProps> = ({ users, products, orders, onAddUser, onUpdateUser, onDeleteUser, onUpdateOrderStatus }) => {
  const [showAddUser, setShowAddUser] = useState(false);
  const [userForm, setUserForm] = useState({ name: '', email: '', role: UserRole.BUYER });

  const totalRev = orders.reduce((a, b) => a + b.total, 0);

  return (
    <div className="space-y-8">
      <div className="grid grid-cols-3 gap-4">
        <div className="bg-white p-6 rounded-2xl border shadow-sm">
          <div className="text-gray-400 text-xs font-bold uppercase">Revenue</div>
          <div className="text-2xl font-black">${totalRev.toFixed(2)}</div>
        </div>
        <div className="bg-white p-6 rounded-2xl border shadow-sm">
          <div className="text-gray-400 text-xs font-bold uppercase">Users</div>
          <div className="text-2xl font-black">{users.length}</div>
        </div>
        <div className="bg-white p-6 rounded-2xl border shadow-sm">
          <div className="text-gray-400 text-xs font-bold uppercase">Orders</div>
          <div className="text-2xl font-black">{orders.length}</div>
        </div>
      </div>

      <div className="bg-white rounded-2xl border p-6">
        <div className="flex justify-between items-center mb-6">
          <h3 className="font-bold">User Directory</h3>
          <button onClick={() => setShowAddUser(true)} className="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold">+ New User</button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-sm">
            <thead className="border-b text-gray-400">
              <tr>
                <th className="pb-3">User</th>
                <th className="pb-3">Role</th>
                <th className="pb-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {users.map(u => (
                <tr key={u.id}>
                  <td className="py-4">
                    <div className="font-bold">{u.name}</div>
                    <div className="text-xs text-gray-500">{u.email}</div>
                  </td>
                  <td className="py-4">
                    <select 
                      value={u.role} 
                      onChange={e => onUpdateUser(u.id, { role: e.target.value as UserRole })}
                      className="bg-gray-50 border rounded p-1 text-xs"
                    >
                      {Object.values(UserRole).map(r => <option key={r} value={r}>{r}</option>)}
                    </select>
                  </td>
                  <td className="py-4 text-right">
                    <button onClick={() => onDeleteUser(u.id)} className="text-red-500 hover:underline">Delete</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className="bg-white rounded-2xl border p-6">
        <h3 className="font-bold mb-6">Recent Orders</h3>
        <div className="space-y-4">
          {orders.map(o => (
            <div key={o.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div>
                <div className="font-bold">Order #{o.id.slice(0, 8)}</div>
                <div className="text-xs text-gray-500">${o.total} • {o.deliveryLocation}</div>
              </div>
              <select 
                value={o.status}
                onChange={e => onUpdateOrderStatus(o.id, e.target.value as Order['status'])}
                className="text-xs font-bold bg-white border rounded p-1"
              >
                <option value="PENDING">Pending</option>
                <option value="PAID">Paid</option>
                <option value="DELIVERED">Delivered</option>
              </select>
            </div>
          ))}
          {orders.length === 0 && <p className="text-center text-gray-400 italic">No orders yet.</p>}
        </div>
      </div>

      {showAddUser && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 className="font-bold mb-4">Register New User</h3>
            <div className="space-y-4">
              <input type="text" placeholder="Full Name" value={userForm.name} onChange={e => setUserForm({...userForm, name: e.target.value})} className="w-full border rounded-xl p-2" />
              <input type="email" placeholder="Email Address" value={userForm.email} onChange={e => setUserForm({...userForm, email: e.target.value})} className="w-full border rounded-xl p-2" />
              <select value={userForm.role} onChange={e => setUserForm({...userForm, role: e.target.value as UserRole})} className="w-full border rounded-xl p-2">
                {Object.values(UserRole).map(r => <option key={r} value={r}>{r}</option>)}
              </select>
              <div className="flex space-x-3 mt-6">
                <button onClick={() => setShowAddUser(false)} className="flex-1 border py-2 rounded-xl">Cancel</button>
                <button onClick={() => { onAddUser(userForm); setShowAddUser(false); }} className="flex-1 bg-emerald-600 text-white py-2 rounded-xl">Create</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AdminDashboard;
