// src/pages/auth/Register.tsx
import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { RegisterData, Role } from '../../types';
import { Link, useNavigate } from 'react-router-dom';
import { roleService } from '../../services/api';
import './Register.css';

const Register: React.FC = () => {
  const { login, isLoading } = useAuth();
  const navigate = useNavigate();
  const [roles, setRoles] = useState<Role[]>([]);
  const [formData, setFormData] = useState<RegisterData>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: 2, // Por defecto rol de usuario
  });
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [generalError, setGeneralError] = useState<string>('');
  const [loadingRoles, setLoadingRoles] = useState(true);

  // Cargar roles al montar el componente
  useEffect(() => {
    const fetchRoles = async () => {
      try {
        const rolesData = await roleService.getRoles();
        setRoles(rolesData);
        if (rolesData.length > 0) {
          // Buscar rol "user" por defecto, si no existe usar el primero
          const userRole = rolesData.find((role: Role) => role.name === 'user');
          setFormData(prev => ({
            ...prev,
            role_id: userRole ? userRole.id : rolesData[0].id
          }));
        }
      } catch (error) {
        console.error('Error al cargar roles:', error);
      } finally {
        setLoadingRoles(false);
      }
    };

    fetchRoles();
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: name === 'role_id' ? Number(value) : value,
    });

    // Limpiar errores del campo actual
    if (errors[name]) {
      setErrors(prev => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string[]> = {};

    // Validar nombre
    if (!formData.name.trim()) {
      newErrors.name = ['El nombre es obligatorio'];
    } else if (formData.name.trim().length < 2) {
      newErrors.name = ['El nombre debe tener al menos 2 caracteres'];
    }

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email.trim()) {
      newErrors.email = ['El correo electrónico es obligatorio'];
    } else if (!emailRegex.test(formData.email)) {
      newErrors.email = ['El correo electrónico no es válido'];
    }

    // Validar contraseña
    if (!formData.password) {
      newErrors.password = ['La contraseña es obligatoria'];
    } else if (formData.password.length < 8) {
      newErrors.password = ['La contraseña debe tener al menos 8 caracteres'];
    }

    // Validar confirmación de contraseña
    if (!formData.password_confirmation) {
      newErrors.password_confirmation = ['Debes confirmar la contraseña'];
    } else if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = ['Las contraseñas no coinciden'];
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setGeneralError('');

    if (!validateForm()) {
      return;
    }

    try {
      // Registrar usuario usando el servicio de autenticación
      const response = await fetch(`${import.meta.env.VITE_API_URL}/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      if (!response.ok) {
        const errorData = await response.json();
        if (errorData.errors) {
          setErrors(errorData.errors);
        } else {
          setGeneralError(errorData.message || 'Error al registrar usuario');
        }
        return;
      }

      const userData = await response.json();
      
      // Hacer login automático después del registro exitoso
      await login({
        email: formData.email,
        password: formData.password,
      });

      navigate('/dashboard');
    } catch (error: any) {
      setGeneralError('Error de conexión. Por favor, inténtalo de nuevo.');
    }
  };

  if (loadingRoles) {
    return (
      <div className="register-container">
        <div className="register-card">
          <div className="loading-state">
            <div className="loading-spinner-large"></div>
            <p>Cargando...</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="register-container">
      <div className="register-card">
        <div className="register-header">
          <h2 className="register-title">Crear Cuenta</h2>
          <p className="register-subtitle">Completa el formulario para registrarte</p>
        </div>

        <form className="register-form" onSubmit={handleSubmit}>
          {generalError && (
            <div className="error-message">
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
              </svg>
              {generalError}
            </div>
          )}
          
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="name" className="form-label">
                Nombre Completo
              </label>
              <input
                id="name"
                name="name"
                type="text"
                required
                value={formData.name}
                onChange={handleChange}
                className={`form-input ${errors.name ? 'error' : ''}`}
                placeholder="Ej: Juan Pérez"
                autoComplete="name"
              />
              {errors.name && (
                <div className="field-error">
                  {errors.name[0]}
                </div>
              )}
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="email" className="form-label">
              Correo Electrónico
            </label>
            <input
              id="email"
              name="email"
              type="email"
              required
              value={formData.email}
              onChange={handleChange}
              className={`form-input ${errors.email ? 'error' : ''}`}
              placeholder="ejemplo@correo.com"
              autoComplete="email"
            />
            {errors.email && (
              <div className="field-error">
                {errors.email[0]}
              </div>
            )}
          </div>
          
          <div className="form-row">
            <div className="form-group">
              <label htmlFor="password" className="form-label">
                Contraseña
              </label>
              <input
                id="password"
                name="password"
                type="password"
                required
                value={formData.password}
                onChange={handleChange}
                className={`form-input ${errors.password ? 'error' : ''}`}
                placeholder="Mínimo 8 caracteres"
                autoComplete="new-password"
              />
              {errors.password && (
                <div className="field-error">
                  {errors.password[0]}
                </div>
              )}
            </div>

            <div className="form-group">
              <label htmlFor="password_confirmation" className="form-label">
                Confirmar Contraseña
              </label>
              <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                value={formData.password_confirmation}
                onChange={handleChange}
                className={`form-input ${errors.password_confirmation ? 'error' : ''}`}
                placeholder="Repite la contraseña"
                autoComplete="new-password"
              />
              {errors.password_confirmation && (
                <div className="field-error">
                  {errors.password_confirmation[0]}
                </div>
              )}
            </div>
          </div>

          <button
            type="submit"
            disabled={isLoading}
            className="submit-button"
          >
            {isLoading && <div className="loading-spinner"></div>}
            {isLoading ? 'Creando cuenta...' : 'Crear Cuenta'}
          </button>
        </form>

        <div className="register-footer">
          <p>
            ¿Ya tienes una cuenta?{' '}
            <Link to="/login" className="register-link">
              Inicia sesión aquí
            </Link>
          </p>
        </div>
      </div>
    </div>
  );
};

export default Register;