/*
  # StarRent.vip Database Schema

  1. New Tables
    - `users` - User accounts and profiles
    - `routers` - Starlink router inventory
    - `rentals` - Rental bookings and history
    - `payments` - Payment transactions via Plisio
    - `transactions` - Financial transaction history
    - `support_tickets` - Customer support system
    - `router_features` - Router feature specifications
    - `rental_reviews` - Customer reviews and ratings
    - `payment_methods` - Supported payment methods
    - `notifications` - User notifications
    - `audit_logs` - System audit trail

  2. Security
    - Enable RLS on all tables
    - Add policies for user data access
    - Secure payment and transaction data

  3. Features
    - User authentication and profiles
    - Router inventory management
    - Rental booking system
    - Payment processing integration
    - Review and rating system
    - Support ticket system
*/

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  email text UNIQUE NOT NULL,
  name text NOT NULL,
  username text UNIQUE,
  phone text,
  address text,
  city text,
  country text,
  postal_code text,
  balance decimal(20,2) DEFAULT 0.00,
  total_spent decimal(20,2) DEFAULT 0.00,
  referral_code text UNIQUE,
  referred_by uuid REFERENCES users(id),
  email_verified boolean DEFAULT false,
  phone_verified boolean DEFAULT false,
  kyc_status text DEFAULT 'pending' CHECK (kyc_status IN ('pending', 'approved', 'rejected')),
  kyc_data jsonb,
  status text DEFAULT 'active' CHECK (status IN ('active', 'suspended', 'banned')),
  avatar_url text,
  preferences jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Router features table
CREATE TABLE IF NOT EXISTS router_features (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  name text NOT NULL,
  description text,
  icon text,
  category text,
  created_at timestamptz DEFAULT now()
);

-- Routers table
CREATE TABLE IF NOT EXISTS routers (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  name text NOT NULL,
  model text NOT NULL,
  description text,
  daily_rate decimal(10,2) NOT NULL,
  weekly_rate decimal(10,2),
  monthly_rate decimal(10,2) NOT NULL,
  deposit_required decimal(10,2) NOT NULL,
  max_speed text NOT NULL,
  coverage_area text,
  weight text,
  dimensions text,
  power_consumption text,
  operating_temperature text,
  features text[] DEFAULT '{}',
  feature_ids uuid[] DEFAULT '{}',
  images text[] DEFAULT '{}',
  primary_image text,
  status text DEFAULT 'available' CHECK (status IN ('available', 'rented', 'maintenance', 'retired')),
  location text,
  serial_number text UNIQUE,
  purchase_date date,
  warranty_expiry date,
  last_maintenance timestamptz,
  total_rental_days integer DEFAULT 0,
  total_revenue decimal(20,2) DEFAULT 0.00,
  rating decimal(3,2) DEFAULT 0.00,
  review_count integer DEFAULT 0,
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Rentals table
CREATE TABLE IF NOT EXISTS rentals (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  rental_number text UNIQUE NOT NULL,
  user_id uuid NOT NULL REFERENCES users(id),
  router_id uuid NOT NULL REFERENCES routers(id),
  start_date date NOT NULL,
  end_date date NOT NULL,
  total_days integer NOT NULL,
  daily_rate decimal(10,2) NOT NULL,
  subtotal decimal(20,2) NOT NULL,
  deposit_amount decimal(20,2) NOT NULL,
  tax_amount decimal(20,2) DEFAULT 0.00,
  total_amount decimal(20,2) NOT NULL,
  currency text DEFAULT 'USD',
  status text DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'active', 'completed', 'cancelled', 'refunded')),
  payment_status text DEFAULT 'pending' CHECK (payment_status IN ('pending', 'paid', 'partial', 'refunded', 'failed')),
  delivery_method text DEFAULT 'pickup' CHECK (delivery_method IN ('pickup', 'delivery', 'shipping')),
  delivery_address text,
  delivery_instructions text,
  pickup_location text,
  notes text,
  cancellation_reason text,
  cancelled_at timestamptz,
  confirmed_at timestamptz,
  started_at timestamptz,
  completed_at timestamptz,
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Payment methods table
CREATE TABLE IF NOT EXISTS payment_methods (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  name text NOT NULL,
  code text UNIQUE NOT NULL,
  type text NOT NULL CHECK (type IN ('cryptocurrency', 'fiat', 'stablecoin')),
  symbol text,
  icon_url text,
  min_amount decimal(20,8),
  max_amount decimal(20,8),
  fee_percentage decimal(5,4) DEFAULT 0.0000,
  fee_fixed decimal(20,8) DEFAULT 0.00000000,
  confirmation_blocks integer DEFAULT 1,
  network text,
  contract_address text,
  decimals integer DEFAULT 8,
  status text DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'maintenance')),
  sort_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  payment_number text UNIQUE NOT NULL,
  user_id uuid NOT NULL REFERENCES users(id),
  rental_id uuid REFERENCES rentals(id),
  amount decimal(20,2) NOT NULL,
  currency text DEFAULT 'USD',
  crypto_amount decimal(20,8),
  crypto_currency text,
  exchange_rate decimal(20,8),
  payment_method_id uuid REFERENCES payment_methods(id),
  payment_type text NOT NULL CHECK (payment_type IN ('rental', 'deposit', 'refund', 'fee')),
  status text DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'completed', 'failed', 'expired', 'cancelled')),
  plisio_invoice_id text,
  plisio_txn_id text,
  blockchain_txn_id text,
  blockchain_confirmations integer DEFAULT 0,
  required_confirmations integer DEFAULT 1,
  wallet_address text,
  callback_url text,
  success_url text,
  cancel_url text,
  expires_at timestamptz,
  confirmed_at timestamptz,
  failed_at timestamptz,
  failure_reason text,
  webhook_data jsonb,
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  transaction_number text UNIQUE NOT NULL,
  user_id uuid NOT NULL REFERENCES users(id),
  rental_id uuid REFERENCES rentals(id),
  payment_id uuid REFERENCES payments(id),
  type text NOT NULL CHECK (type IN ('deposit', 'rental_payment', 'refund', 'withdrawal', 'fee', 'bonus', 'penalty')),
  amount decimal(20,2) NOT NULL,
  currency text DEFAULT 'USD',
  balance_before decimal(20,2) NOT NULL,
  balance_after decimal(20,2) NOT NULL,
  description text NOT NULL,
  reference text,
  status text DEFAULT 'completed' CHECK (status IN ('pending', 'completed', 'failed', 'reversed')),
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Rental reviews table
CREATE TABLE IF NOT EXISTS rental_reviews (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  rental_id uuid NOT NULL REFERENCES rentals(id),
  user_id uuid NOT NULL REFERENCES users(id),
  router_id uuid NOT NULL REFERENCES routers(id),
  rating integer NOT NULL CHECK (rating >= 1 AND rating <= 5),
  title text,
  comment text,
  pros text[],
  cons text[],
  would_recommend boolean DEFAULT true,
  verified_rental boolean DEFAULT false,
  helpful_votes integer DEFAULT 0,
  status text DEFAULT 'published' CHECK (status IN ('draft', 'published', 'hidden', 'flagged')),
  moderated_at timestamptz,
  moderated_by uuid REFERENCES users(id),
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now(),
  UNIQUE(rental_id, user_id)
);

-- Support tickets table
CREATE TABLE IF NOT EXISTS support_tickets (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  ticket_number text UNIQUE NOT NULL,
  user_id uuid NOT NULL REFERENCES users(id),
  rental_id uuid REFERENCES rentals(id),
  subject text NOT NULL,
  description text NOT NULL,
  category text NOT NULL CHECK (category IN ('technical', 'billing', 'general', 'complaint', 'feature_request')),
  priority text DEFAULT 'medium' CHECK (priority IN ('low', 'medium', 'high', 'urgent')),
  status text DEFAULT 'open' CHECK (status IN ('open', 'in_progress', 'waiting_customer', 'resolved', 'closed')),
  assigned_to uuid REFERENCES users(id),
  resolution text,
  satisfaction_rating integer CHECK (satisfaction_rating >= 1 AND satisfaction_rating <= 5),
  satisfaction_comment text,
  first_response_at timestamptz,
  resolved_at timestamptz,
  closed_at timestamptz,
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

-- Support ticket messages table
CREATE TABLE IF NOT EXISTS support_ticket_messages (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  ticket_id uuid NOT NULL REFERENCES support_tickets(id),
  user_id uuid NOT NULL REFERENCES users(id),
  message text NOT NULL,
  is_internal boolean DEFAULT false,
  attachments text[],
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now()
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id uuid NOT NULL REFERENCES users(id),
  type text NOT NULL,
  title text NOT NULL,
  message text NOT NULL,
  data jsonb DEFAULT '{}',
  read boolean DEFAULT false,
  read_at timestamptz,
  action_url text,
  expires_at timestamptz,
  created_at timestamptz DEFAULT now()
);

-- Audit logs table
CREATE TABLE IF NOT EXISTS audit_logs (
  id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_id uuid REFERENCES users(id),
  action text NOT NULL,
  resource_type text NOT NULL,
  resource_id uuid,
  old_values jsonb,
  new_values jsonb,
  ip_address inet,
  user_agent text,
  metadata jsonb DEFAULT '{}',
  created_at timestamptz DEFAULT now()
);

-- Enable Row Level Security
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE routers ENABLE ROW LEVEL SECURITY;
ALTER TABLE rentals ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE transactions ENABLE ROW LEVEL SECURITY;
ALTER TABLE rental_reviews ENABLE ROW LEVEL SECURITY;
ALTER TABLE support_tickets ENABLE ROW LEVEL SECURITY;
ALTER TABLE support_ticket_messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE audit_logs ENABLE ROW LEVEL SECURITY;

-- RLS Policies

-- Users policies
CREATE POLICY "Users can read own data"
  ON users
  FOR SELECT
  TO authenticated
  USING (auth.uid() = id);

CREATE POLICY "Users can update own data"
  ON users
  FOR UPDATE
  TO authenticated
  USING (auth.uid() = id);

-- Routers policies (public read)
CREATE POLICY "Anyone can read available routers"
  ON routers
  FOR SELECT
  TO anon, authenticated
  USING (status = 'available');

-- Rentals policies
CREATE POLICY "Users can read own rentals"
  ON rentals
  FOR SELECT
  TO authenticated
  USING (user_id = auth.uid());

CREATE POLICY "Users can create own rentals"
  ON rentals
  FOR INSERT
  TO authenticated
  WITH CHECK (user_id = auth.uid());

CREATE POLICY "Users can update own rentals"
  ON rentals
  FOR UPDATE
  TO authenticated
  USING (user_id = auth.uid());

-- Payments policies
CREATE POLICY "Users can read own payments"
  ON payments
  FOR SELECT
  TO authenticated
  USING (user_id = auth.uid());

CREATE POLICY "Users can create own payments"
  ON payments
  FOR INSERT
  TO authenticated
  WITH CHECK (user_id = auth.uid());

-- Transactions policies
CREATE POLICY "Users can read own transactions"
  ON transactions
  FOR SELECT
  TO authenticated
  USING (user_id = auth.uid());

-- Reviews policies
CREATE POLICY "Anyone can read published reviews"
  ON rental_reviews
  FOR SELECT
  TO anon, authenticated
  USING (status = 'published');

CREATE POLICY "Users can create reviews for own rentals"
  ON rental_reviews
  FOR INSERT
  TO authenticated
  WITH CHECK (user_id = auth.uid());

CREATE POLICY "Users can update own reviews"
  ON rental_reviews
  FOR UPDATE
  TO authenticated
  USING (user_id = auth.uid());

-- Support tickets policies
CREATE POLICY "Users can read own tickets"
  ON support_tickets
  FOR SELECT
  TO authenticated
  USING (user_id = auth.uid());

CREATE POLICY "Users can create own tickets"
  ON support_tickets
  FOR INSERT
  TO authenticated
  WITH CHECK (user_id = auth.uid());

-- Support ticket messages policies
CREATE POLICY "Users can read messages for own tickets"
  ON support_ticket_messages
  FOR SELECT
  TO authenticated
  USING (
    EXISTS (
      SELECT 1 FROM support_tickets 
      WHERE id = ticket_id AND user_id = auth.uid()
    )
  );

CREATE POLICY "Users can create messages for own tickets"
  ON support_ticket_messages
  FOR INSERT
  TO authenticated
  WITH CHECK (
    user_id = auth.uid() AND
    EXISTS (
      SELECT 1 FROM support_tickets 
      WHERE id = ticket_id AND user_id = auth.uid()
    )
  );

-- Notifications policies
CREATE POLICY "Users can read own notifications"
  ON notifications
  FOR SELECT
  TO authenticated
  USING (user_id = auth.uid());

CREATE POLICY "Users can update own notifications"
  ON notifications
  FOR UPDATE
  TO authenticated
  USING (user_id = auth.uid());

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_referral_code ON users(referral_code);
CREATE INDEX IF NOT EXISTS idx_routers_status ON routers(status);
CREATE INDEX IF NOT EXISTS idx_routers_rating ON routers(rating DESC);
CREATE INDEX IF NOT EXISTS idx_rentals_user_id ON rentals(user_id);
CREATE INDEX IF NOT EXISTS idx_rentals_router_id ON rentals(router_id);
CREATE INDEX IF NOT EXISTS idx_rentals_status ON rentals(status);
CREATE INDEX IF NOT EXISTS idx_rentals_dates ON rentals(start_date, end_date);
CREATE INDEX IF NOT EXISTS idx_payments_user_id ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_rental_id ON payments(rental_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_payments_plisio_invoice_id ON payments(plisio_invoice_id);
CREATE INDEX IF NOT EXISTS idx_transactions_user_id ON transactions(user_id);
CREATE INDEX IF NOT EXISTS idx_transactions_type ON transactions(type);
CREATE INDEX IF NOT EXISTS idx_reviews_router_id ON rental_reviews(router_id);
CREATE INDEX IF NOT EXISTS idx_reviews_rating ON rental_reviews(rating);
CREATE INDEX IF NOT EXISTS idx_tickets_user_id ON support_tickets(user_id);
CREATE INDEX IF NOT EXISTS idx_tickets_status ON support_tickets(status);
CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_read ON notifications(read);

-- Create functions for automatic updates
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_routers_updated_at BEFORE UPDATE ON routers FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_rentals_updated_at BEFORE UPDATE ON rentals FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_payments_updated_at BEFORE UPDATE ON payments FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_transactions_updated_at BEFORE UPDATE ON transactions FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_reviews_updated_at BEFORE UPDATE ON rental_reviews FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_tickets_updated_at BEFORE UPDATE ON support_tickets FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Function to generate unique rental numbers
CREATE OR REPLACE FUNCTION generate_rental_number()
RETURNS TEXT AS $$
BEGIN
  RETURN 'SR' || TO_CHAR(NOW(), 'YYYYMMDD') || '-' || LPAD(NEXTVAL('rental_number_seq')::TEXT, 4, '0');
END;
$$ LANGUAGE plpgsql;

-- Create sequence for rental numbers
CREATE SEQUENCE IF NOT EXISTS rental_number_seq START 1000;

-- Function to generate unique payment numbers
CREATE OR REPLACE FUNCTION generate_payment_number()
RETURNS TEXT AS $$
BEGIN
  RETURN 'PAY' || TO_CHAR(NOW(), 'YYYYMMDD') || '-' || LPAD(NEXTVAL('payment_number_seq')::TEXT, 6, '0');
END;
$$ LANGUAGE plpgsql;

-- Create sequence for payment numbers
CREATE SEQUENCE IF NOT EXISTS payment_number_seq START 100000;

-- Function to generate unique transaction numbers
CREATE OR REPLACE FUNCTION generate_transaction_number()
RETURNS TEXT AS $$
BEGIN
  RETURN 'TXN' || TO_CHAR(NOW(), 'YYYYMMDD') || '-' || LPAD(NEXTVAL('transaction_number_seq')::TEXT, 6, '0');
END;
$$ LANGUAGE plpgsql;

-- Create sequence for transaction numbers
CREATE SEQUENCE IF NOT EXISTS transaction_number_seq START 100000;

-- Function to generate unique ticket numbers
CREATE OR REPLACE FUNCTION generate_ticket_number()
RETURNS TEXT AS $$
BEGIN
  RETURN 'TKT' || TO_CHAR(NOW(), 'YYYYMMDD') || '-' || LPAD(NEXTVAL('ticket_number_seq')::TEXT, 4, '0');
END;
$$ LANGUAGE plpgsql;

-- Create sequence for ticket numbers
CREATE SEQUENCE IF NOT EXISTS ticket_number_seq START 1000;

-- Function to update router rating when review is added/updated
CREATE OR REPLACE FUNCTION update_router_rating()
RETURNS TRIGGER AS $$
BEGIN
  UPDATE routers 
  SET 
    rating = (
      SELECT ROUND(AVG(rating)::numeric, 2)
      FROM rental_reviews 
      WHERE router_id = NEW.router_id AND status = 'published'
    ),
    review_count = (
      SELECT COUNT(*)
      FROM rental_reviews 
      WHERE router_id = NEW.router_id AND status = 'published'
    )
  WHERE id = NEW.router_id;
  
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger for router rating updates
CREATE TRIGGER update_router_rating_trigger
  AFTER INSERT OR UPDATE ON rental_reviews
  FOR EACH ROW
  EXECUTE FUNCTION update_router_rating();