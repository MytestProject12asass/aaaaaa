import React from 'react'
import { Link } from 'react-router-dom'
import { Wifi, Users, MapPin, Clock, Star, ArrowRight } from 'lucide-react'
import { motion } from 'framer-motion'
import { formatCurrency } from '../lib/utils'

interface RouterCardProps {
  router: {
    id: string
    name: string
    model: string
    description: string
    daily_rate: number
    monthly_rate: number
    deposit_required: number
    max_speed: string
    coverage_area: string
    features: string[]
    image_url: string
    status: 'available' | 'rented' | 'maintenance'
    rating?: number
    reviews?: number
  }
  className?: string
}

const RouterCard: React.FC<RouterCardProps> = ({ router, className = '' }) => {
  const isAvailable = router.status === 'available'
  
  const statusConfig = {
    available: {
      badge: 'Available',
      badgeClass: 'bg-green-100 text-green-800',
      buttonText: 'Rent Now',
      buttonClass: 'btn-primary',
    },
    rented: {
      badge: 'Rented',
      badgeClass: 'bg-red-100 text-red-800',
      buttonText: 'Join Waitlist',
      buttonClass: 'btn-secondary',
    },
    maintenance: {
      badge: 'Maintenance',
      badgeClass: 'bg-yellow-100 text-yellow-800',
      buttonText: 'Notify Me',
      buttonClass: 'btn-secondary',
    },
  }

  const config = statusConfig[router.status]

  return (
    <motion.div
      whileHover={{ y: -4 }}
      transition={{ duration: 0.2 }}
      className={`router-card rounded-2xl p-6 ${className}`}
    >
      {/* Router Image */}
      <div className="relative mb-6 overflow-hidden rounded-xl">
        <img
          src={router.image_url}
          alt={router.name}
          className="w-full h-48 object-cover transition-transform duration-300 hover:scale-105"
        />
        
        {/* Status Badge */}
        <div className="absolute top-3 left-3">
          <span className={`px-3 py-1 rounded-full text-xs font-medium ${config.badgeClass}`}>
            {config.badge}
          </span>
        </div>

        {/* Rating */}
        {router.rating && (
          <div className="absolute top-3 right-3 bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1 flex items-center space-x-1">
            <Star className="h-4 w-4 text-yellow-400 fill-current" />
            <span className="text-sm font-medium">{router.rating}</span>
            {router.reviews && (
              <span className="text-xs text-gray-500">({router.reviews})</span>
            )}
          </div>
        )}
      </div>

      {/* Router Info */}
      <div className="mb-4">
        <h3 className="text-xl font-bold text-gray-900 mb-2">{router.name}</h3>
        <p className="text-sm text-gray-600 mb-3 line-clamp-2">{router.description}</p>
        
        {/* Specs */}
        <div className="grid grid-cols-2 gap-3 text-sm">
          <div className="flex items-center space-x-2">
            <Wifi className="h-4 w-4 text-primary-600" />
            <span className="text-gray-600">Up to {router.max_speed}</span>
          </div>
          <div className="flex items-center space-x-2">
            <MapPin className="h-4 w-4 text-primary-600" />
            <span className="text-gray-600">{router.coverage_area}</span>
          </div>
        </div>
      </div>

      {/* Features */}
      <div className="mb-6">
        <div className="flex flex-wrap gap-2">
          {router.features.slice(0, 3).map((feature, index) => (
            <span
              key={index}
              className="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-md"
            >
              {feature}
            </span>
          ))}
          {router.features.length > 3 && (
            <span className="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-md">
              +{router.features.length - 3} more
            </span>
          )}
        </div>
      </div>

      {/* Pricing */}
      <div className="mb-6 p-4 bg-gray-50 rounded-lg">
        <div className="flex justify-between items-center mb-2">
          <span className="text-sm text-gray-600">Daily Rate</span>
          <span className="text-lg font-bold text-gray-900">
            {formatCurrency(router.daily_rate)}/day
          </span>
        </div>
        <div className="flex justify-between items-center mb-2">
          <span className="text-sm text-gray-600">Monthly Rate</span>
          <span className="text-lg font-bold text-primary-600">
            {formatCurrency(router.monthly_rate)}/month
          </span>
        </div>
        <div className="flex justify-between items-center">
          <span className="text-sm text-gray-600">Security Deposit</span>
          <span className="text-sm font-medium text-gray-900">
            {formatCurrency(router.deposit_required)}
          </span>
        </div>
      </div>

      {/* Actions */}
      <div className="space-y-3">
        <Link
          to={`/routers/${router.id}`}
          className={`w-full ${config.buttonClass} text-center group`}
          onClick={(e) => !isAvailable && e.preventDefault()}
        >
          {config.buttonText}
          <ArrowRight className="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform duration-200" />
        </Link>
        
        <Link
          to={`/routers/${router.id}`}
          className="w-full text-center py-2 text-primary-600 hover:text-primary-700 font-medium transition-colors duration-200"
        >
          View Details
        </Link>
      </div>
    </motion.div>
  )
}

export default RouterCard