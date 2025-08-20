import { RegisterData, LoginData, AuthResponse } from '../types';

const API_URL = import.meta.env.VITE_API_URL;

/**
 * Maneja las llamadas a la API de autenticación.
 */
const handleResponse = async (response: Response) => {
  const data = await response.json();
  if (!response.ok) {
    // Lanza un error que puede ser capturado y manejado en el componente.
    // Esto incluye errores de validación (422) y otros errores del servidor.
    throw data;
  }
  return data;
};

const register = (data: RegisterData): Promise<AuthResponse> => {
  return fetch(`${API_URL}/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify(data),
  }).then(handleResponse);
};

export const authService = {
  register,
};
