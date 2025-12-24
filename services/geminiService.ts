
import { GoogleGenAI } from "@google/genai";

const ai = new GoogleGenAI({ apiKey: process.env.API_KEY || '' });

export const getMarketAdvice = async (productName: string) => {
  try {
    const response = await ai.models.generateContent({
      model: 'gemini-3-flash-preview',
      contents: `Provide a short, 2-sentence market insight for selling ${productName} in a local agricultural market. Focus on pricing trends or storage tips.`,
    });
    return response.text;
  } catch (error) {
    console.error("Gemini Error:", error);
    return "Market data currently unavailable. Prices remain stable.";
  }
};

export const generateProductDescription = async (name: string, category: string) => {
  try {
    const response = await ai.models.generateContent({
      model: 'gemini-3-flash-preview',
      contents: `Generate a compelling 20-word marketing description for an agricultural product named "${name}" in the "${category}" category.`,
    });
    return response.text;
  } catch (error) {
    return `Fresh and high-quality ${name} direct from the farm.`;
  }
};
